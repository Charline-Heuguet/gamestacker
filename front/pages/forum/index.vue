<template>
  <!-- Bannière et message de bienvenue -->
<div class="w-full mb-8 relative shadow-lg banner bg-black flex flex-col items-start justify-center px-6 md:px-12 py-16 text-center md:text-left">
    <!-- Titre de bienvenue -->
    <h1 class="text-2xl md:text-4xl font-bold text-emerald-500 mb-4">
        Bienvenue sur le forum des passionnés de jeux vidéo
    </h1>
    <!-- Texte d'introduction -->
    <p class="text-base md:text-lg text-gray-100 max-w-2xl">
        Posez vos questions, partagez vos expériences et trouvez des partenaires de jeu. Rejoignez la communauté pour découvrir et discuter des derniers sujets et tendances dans l’univers du gaming.
    </p>
    <!-- Bouton d'appel à l'action -->
    <button class="mt-4 bg-emerald-500 text-white font-semibold py-2 px-4 rounded-full hover:bg-emerald-600">
        Commencer une discussion
    </button>
</div>

  <!-- Contenu principal (Forums) -->
  <div class="forum-container bg-white py-12 px-4 rounded-lg ">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Liste des Forums</h1>

    <!-- Barre de recherche -->
    <input
      v-model="searchTerm"
      @input="searchForums"
      placeholder="Rechercher un forum"
      class="w-full p-3 mb-8 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-900 focus:outline-none focus:border-emerald-500"
    />

    <div v-if="forums.length" class="space-y-8">
      <div v-for="forum in forums" :key="forum.id" class="forum-item bg-gray-100 p-6 rounded-lg shadow-neumorphism">
        <a v-if="forum.id" :href="`/forum/${forum.id}`" class="text-xl font-semibold text-emerald-600 underline mb-2">
          {{ forum.title }}
        </a>
        <p v-else class="text-red-500">Erreur : ID manquant pour cet article.</p>
        <p class="text-gray-700 mb-2">{{ forum.content }}</p>
        <p class="text-gray-600"><strong>Date :</strong> {{ formatDate(forum.date) }}</p>
        <p class="text-gray-600"><strong>Utilisateur :</strong> {{ forum.user ? forum.user.pseudo : 'Anonyme' }}</p>
        <hr class="my-4 border-gray-300" />
      </div>

      <!-- Pagination -->
      <div class="flex justify-center space-x-4 mt-6">
        <button @click="prevPage" :disabled="page <= 1" class="btn-pagination" :class="{ 'opacity-50 cursor-not-allowed': page <= 1 }">
          Page précédente
        </button>
        <button @click="nextPage" :disabled="isLastPage" class="btn-pagination" :class="{ 'opacity-50 cursor-not-allowed': isLastPage }">
          Page suivante
        </button>
      </div>
    </div>

    <p v-else class="text-center text-gray-500 mt-8">Aucun forum disponible pour le moment.</p>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const forums = ref([]);
const errorMessage = ref(null);
const page = ref(1);
const isLastPage = ref(false);
const totalItems = ref(0);
const itemsPerPage = 10;
const searchTerm = ref('');

// Format de date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Récupération des forums
const fetchForums = async (pageNum = 1, term = '') => {
  try {
    const response = await fetch(`http://localhost:8000/api/forum?page=${pageNum}&search=${term}`, {
      headers: {
        'Content-Type': 'application/json',
      },
    });
    if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
    const data = await response.json();
    forums.value = data.items || [];
    totalItems.value = data.totalItems;
    isLastPage.value = pageNum * itemsPerPage >= totalItems.value;
  } catch (error) {
    errorMessage.value = `Erreur : ${error.message}`;
  }
};

// Charger les forums initialement
fetchForums(page.value, searchTerm.value);

const searchForums = () => {
  page.value = 1;
  fetchForums(page.value, searchTerm.value);
};

const nextPage = () => {
  if (!isLastPage.value) {
    page.value += 1;
    fetchForums(page.value, searchTerm.value);
  }
};

const prevPage = () => {
  if (page.value > 1) {
    page.value -= 1;
    fetchForums(page.value, searchTerm.value);
  }
};
</script>

<style scoped>
/* Style général */
.forum-container {
    max-width: 900px;
    margin: 0 auto;
}

/* Effet de Neumorphisme */
.shadow-neumorphism {
    box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
}

/* Style des boutons de pagination */
.btn-pagination {
    padding: 10px 20px;
    background-color: #34d399;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-pagination:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Message d'erreur */
.error-message {
    color: #f87171;
    margin-top: 15px;
    text-align: center;
}

/* Styles pour la bannière */
.banner {
    background-image: url('@/assets/img/banner.png');
    background-size: cover;
    background-position: center;
    height: 400px;
}

@media (max-width: 768px) {
    .banner {
        height: 300px;
    }
}
</style>
