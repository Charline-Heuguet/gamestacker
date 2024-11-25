<template>
  <div class="forum-page-container min-h-screen bg-white dark:bg-neutral-900 py-12 px-4">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 max-w-7xl mx-auto">
      <!-- Colonne de gauche pour les forums sans commentaires -->
      <div class="no-comments-section col-span-1  p-4 rounded-lg ">
        <NoCommentsForums />
      </div>

      <!-- Colonne principale pour afficher le forum sélectionné -->
      <div v-if="forum" class="forum-details col-span-1 lg:col-span-3 bg-gray-100 dark:bg-neutral-800 p-8 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-emerald-500 mb-4">{{ forum.title }}</h1>
        <div class="flex items-center gap-2 mb-5">
              <img
                :src="forum.user.image ? `${backendUrl}/images/users/${forum.user.image}` : `${backendUrl}/images/default.jpg`"
                :alt="forum.user.pseudo"
                class="w-10 h-10 rounded-full object-cover"
              />
              <p class="text-gray-700 font-medium dark:text-emerald-700">{{ forum.user.pseudo }}</p>
            </div>
        <div class="content mb-6">
          <p class="text-gray-700 dark:text-gray-100">{{ forum.content }}</p>
        </div>
        <p class="text-neutral-600 mb-4">Ticket rédiger le {{ formatDate(forum.date) }}</p>
        <hr class="my-4 border-neutral-300 dark:border-gray-700" />
        <!-- Section des commentaires -->
        <div v-if="forum.comment && forum.comment.length" class="comments-section mt-8">
          <div class="flex justify-between">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-emerald-700 mb-4">Commentaires:</h2>
            <button><a href="#new-comment-anchor" class="text-gray-500 add-comment">Ajouter un commentaire</a></button>
          </div>
          <div v-for="(comment, index) in forum.comment" :key="index" class="comment bg-gray-50 dark:bg-emerald-50 p-4 mb-4 rounded-lg shadow relative">
            <div class="flex items-center gap-2 mb-5">
              <img
                :src="comment.user.image ? `${backendUrl}/images/users/${comment.user.image}` : `${backendUrl}/images/default.jpg`"
                :alt="comment.user.pseudo"
                class="w-10 h-10 rounded-full object-cover"
              />
              <p class=" font-medium text-emerald-700">{{ comment.user.pseudo }} <span class="text-gray-700"> le {{ formatDate(comment.date) }} </span></p>
            </div>
            <p class="text-gray-700">{{ comment.content }}</p>
            <div class="flex items-center gap-4 mt-2 text-gray-400 hover:text-emerald-400">
              <button 
                @click="upvoteComment(comment.id)" 
                :disabled="isCommentUpvoted(comment.id) || isLoading" 
                :class="{ 'upvoted': isCommentUpvoted(comment.id) }"
              >
                <UIcon name="lucide:arrow-big-up " class="w-6 h-6" /> Je trouve cela utile ({{ comment.upvote }})
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
        <p id="new-comment-anchor"></p>
        <AddCommentForum
          targetType="forum"
          :targetId="forum.id"
          @commentAdded="fetchForum"
        />
      </div>
    </div>

    <!-- Message d'erreur -->
    <p v-if="errorMessage" class="error-message text-red-500 text-center mt-6">{{ errorMessage }}</p>

    <!-- Modale de signalement -->
    <div v-if="showReportModal" class="modal-backdrop">
      <div class="modal bg-white dark:bg-gray-800">
        <h2 class="text-xl font-semibold mb-4 dark:text-gray-100">Signaler un commentaire</h2>
        <label for="category" class="block mb-2 dark:text-gray-300">Sélectionnez la catégorie du signalement :</label>
        <select v-model="selectedCategory" id="category" class="p-2 border rounded w-full">
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <button @click="submitReport" class="mt-4 mr-5 bg-red-500 text-white p-2 rounded">Envoyer signalement</button>
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
import NoCommentsForums from '@/components/NoCommentsForums.vue';
const backendUrl = 'http://localhost:8000'



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
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 400px;
}


</style>
