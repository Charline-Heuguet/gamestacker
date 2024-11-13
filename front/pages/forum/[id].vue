<template>
  <div class="forum-article-container min-h-screen bg-white py-12 px-4">
    <div v-if="forum" class="max-w-4xl mx-auto bg-gray-100 p-8 rounded-lg shadow-neumorphism">
      <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ forum.title }}</h1>
      <p class="text-gray-600 mb-4"><strong>Date :</strong> {{ formatDate(forum.date) }}</p>
      <p class="text-gray-600 mb-4"><strong>Auteur :</strong> {{ forum.user ? forum.user.pseudo : 'Anonyme' }}</p>
      <div class="content mb-6">
        <p class="text-gray-700">{{ forum.content }}</p>
      </div>

      <!-- Section des commentaires -->
      <div v-if="forum.comment && forum.comment.length" class="comments-section mt-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Commentaires</h2>
        <div v-for="(comment, index) in forum.comment" :key="index" class="comment bg-gray-50 p-4 mb-4 rounded-lg shadow relative">
          <p class="text-gray-600 mb-1">
            <strong>{{ comment.user ? comment.user.pseudo : 'Anonyme' }}</strong> le {{ formatDate(comment.date) }}
          </p>
          <p class="text-gray-700">{{ comment.content }}</p>
          <div class="flex items-center gap-4 mt-2">
            <button 
              @click="upvoteComment(comment.id)" 
              :disabled="isCommentUpvoted(comment.id) || isLoading" 
              :class="{ 'upvoted': isCommentUpvoted(comment.id) }"
            >
              <UIcon name="lucide:arrow-big-up" class="w-6 h-6" /> Je trouve cela utile ({{ comment.upvote }})
              <UIcon v-if="isLoading && currentUpvoteId === comment.id" name="svg-spinners:3-dots-bounce" class="w-6 h-6 ml-2" />
            </button>

            <!-- Bouton de signalement avec positionnement absolu -->
            <button 
              @click="openReportModal(comment.id)" 
              class="absolute top-5 right-5"
            >
              <UIcon name="lucide:message-square-warning" class="w-6 h-6 text-red-500" />
            </button>
          </div>
        </div>
      </div>
      <p v-else class="text-gray-500">Aucun commentaire pour cet article.</p>

      <!-- Composant d'ajout de commentaire -->
      <AddCommentForum
        targetType="forum"
        :targetId="forum.id"
        @commentAdded="fetchForum"
      />
    </div>

    <!-- Message d'erreur -->
    <p v-if="errorMessage" class="error-message text-red-500 text-center mt-6">{{ errorMessage }}</p>

    <!-- Modale de signalement -->
    <div v-if="showReportModal" class="modal-backdrop">
      <div class="modal">
        <h2 class="text-xl font-semibold mb-4">Signaler un commentaire</h2>
        <label for="category" class="block mb-2">Sélectionnez la catégorie du signalement :</label>
        <select v-model="selectedCategory" id="category" class="p-2 border rounded w-full">
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <button @click="submitReport" class="mt-4 bg-red-500 text-white p-2 rounded">Envoyer signalement</button>
        <button @click="closeReportModal" class="mt-2 text-gray-500">Annuler</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import AddCommentForum from '@/components/AddCommentForum.vue';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const forum = ref(null);
const errorMessage = ref(null);
const authStore = useAuthStore();
const isLoading = ref(false);
const currentUpvoteId = ref(null);

// État pour le signalement
const showReportModal = ref(false);
const selectedCommentId = ref(null);
const selectedCategory = ref(null);
const categories = ref([]);
// Récupération de l'objet $toast depuis le contexte Nuxt
const { $toast } = useNuxtApp();


// Fonctions de gestion du forum et des commentaires
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const fetchForum = async () => {
  const forumId = route.params.id;
  try {
    const response = await fetch(`https://localhost:8000/api/forum/${forumId}`);
    if (!response.ok) throw new Error(`Erreur HTTP ! statut : ${response.status} (${response.statusText})`);
    const forumData = await response.json();
    if (forumData.comment && forumData.comment.length > 0) {
      forumData.comment.sort((a, b) => b.upvote - a.upvote);
    }
    forum.value = forumData;
  } catch (error) {
    errorMessage.value = `Une erreur s'est produite : ${error.message}`;
    console.error("Erreur de récupération de l'article du forum :", error);
  }
};

const isCommentUpvoted = (commentId) => {
  const upvotedComments = JSON.parse(localStorage.getItem('upvoted_comments')) || [];
  return upvotedComments.includes(commentId);
};

const upvoteComment = async (commentId) => {
  isLoading.value = true;
  currentUpvoteId.value = commentId;
  try {
    const response = await fetch(`https://localhost:8000/api/comment/${commentId}/upvote`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authStore.token}`
      }
    });
    if (!response.ok) throw new Error('Erreur lors de l\'upvote du commentaire');
    const data = await response.json();
    const comment = forum.value.comment.find(c => c.id === commentId);
    if (comment) {
      comment.upvote = data.upvotes;
    }
    let upvotedComments = JSON.parse(localStorage.getItem('upvoted_comments')) || [];
    if (!upvotedComments.includes(commentId)) {
      upvotedComments.push(commentId);
      localStorage.setItem('upvoted_comments', JSON.stringify(upvotedComments));
    }
  } catch (error) {
    console.error(error);
  } finally {
    isLoading.value = false;
    currentUpvoteId.value = null;
    $toast.success('Commentaire aimé avec succès !');
  }
};

// Gestion du signalement
const openReportModal = (commentId) => {
  selectedCommentId.value = commentId;
  showReportModal.value = true;
};

const closeReportModal = () => {
  showReportModal.value = false;
  selectedCategory.value = null;
  selectedCommentId.value = null;
};

const fetchCategories = async () => {
  try {
    const response = await fetch('https://localhost:8000/api/categories');
    if (!response.ok) throw new Error("Erreur lors de la récupération des catégories");
    categories.value = await response.json();
  } catch (error) {
    console.error("Erreur lors de la récupération des catégories:", error);
  }
};

const submitReport = async () => {
  if (!selectedCategory.value) {
    alert("Veuillez sélectionner une catégorie de signalement.");
    return;
  }
  try {
    const response = await fetch(`https://localhost:8000/api/comment/${selectedCommentId.value}/report`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authStore.token}`
      },
      body: JSON.stringify({ categoryId: selectedCategory.value })
    });
    if (!response.ok) throw new Error("Erreur lors de l'envoi du signalement");
    closeReportModal();
    $toast.success('Commentaire signalé avec succès !');
  } catch (error) {
    console.error(error);
    $toast.error('Erreur lors du signalement du commentaire');
  }
};

onMounted(() => {
  fetchForum();
  fetchCategories();
});
</script>

<style scoped>
/* Style général */
.forum-article-container {
  max-width: 900px;
  margin: 0 auto;
}

.shadow-neumorphism {
  box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
}

.upvoted {
  color: green;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 400px;
}
</style>
