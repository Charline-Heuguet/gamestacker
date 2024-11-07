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
        <div v-for="(comment, index) in forum.comment" :key="index" class="comment bg-gray-50 p-4 mb-4 rounded-lg shadow">
          <p class="text-gray-600 mb-1">
            <strong>{{ comment.user ? comment.user.pseudo : 'Anonyme' }}</strong> le {{ formatDate(comment.date) }}
          </p>
          <p class="text-gray-700">{{ comment.content }}</p>
          <div class="text-gray-600 mt-2">
            <span class="mr-4">👍 {{ comment.upvote }}</span>
            <span>👎 {{ comment.downvote }}</span>
          </div>
        </div>
      </div>
      <p v-else class="text-gray-500">Aucun commentaire pour cet article.</p>
    </div>

    <!-- Message d'erreur -->
    <p v-if="errorMessage" class="error-message text-red-500 text-center mt-6">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const forum = ref(null);
const errorMessage = ref(null);

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const fetchForum = async () => {
  const forumId = route.params.id;
  try {
    const response = await fetch(`https://localhost:8000/api/forum/${forumId}`);
    
    if (!response.ok) {
      throw new Error(`Erreur HTTP ! statut : ${response.status} (${response.statusText})`);
    }

    forum.value = await response.json();
  } catch (error) {
    errorMessage.value = `Une erreur s'est produite : ${error.message}`;
    console.error('Erreur de récupération de l\'article du forum :', error);
  }
};

onMounted(fetchForum);
</script>

<style scoped>
/* Style général */
.forum-article-container {
  max-width: 900px;
  margin: 0 auto;
}

/* Effet de Neumorphisme */
.shadow-neumorphism {
  box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
}

/* Style du titre */
h1 {
  font-size: 2.5rem;
  color: #333;
}

.comments-section h2 {
  font-size: 2rem;
  color: #333;
}

/* Style des commentaires */
.comment {
  padding: 10px;
  border-radius: 5px;
  font-size: 16px;
  background-color: #f8fafc;
}

.comment .text-gray-600 span {
  font-weight: bold;
}

/* Message d'erreur */
.error-message {
  color: #f87171;
  text-align: center;
}
</style>
