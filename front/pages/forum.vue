<template>
  <div class="forum-container">
    <h1>Liste des Forums</h1>
    
    <!-- Barre de recherche -->
    <input
      v-model="searchTerm"
      @input="searchForums"
      placeholder="Rechercher un forum"
    />

    <div v-if="forums.length">
      <div v-for="forum in forums" :key="forum.id" class="forum-item">
        <h2>{{ forum.title }}</h2>
        <p>{{ forum.content }}</p>
        <p><strong>Date :</strong> {{ formatDate(forum.date) }}</p>
        <p><strong>Utilisateur :</strong> {{ forum.user ? forum.user.pseudo : 'Anonyme' }}</p>
        <hr />
      </div>
      
      <!-- Pagination -->
      <button @click="prevPage" :disabled="page <= 1">Page précédente</button>
      <button @click="nextPage" :disabled="isLastPage">Page suivante</button>
    </div>
    
    <p v-else>Aucun forum disponible pour le moment.</p>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';

const forums = ref([]);
const errorMessage = ref(null);
const page = ref(1);
const isLastPage = ref(false);
const totalItems = ref(0);
const itemsPerPage = 10;
const searchTerm = ref(''); // Nouveau terme de recherche
const router = useRouter();

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Fonction pour récupérer les forums avec un terme de recherche et une page
const fetchForums = async (pageNum = 1, term = '') => {
  try {
    const response = await fetch(`http://localhost:8000/api/forum?page=${pageNum}&search=${term}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error(`Erreur HTTP ! statut : ${response.status} (${response.statusText})`);
    }

    const data = await response.json();
    forums.value = data.items;
    totalItems.value = data.totalItems;
    isLastPage.value = pageNum * itemsPerPage >= totalItems.value;
  } catch (error) {
    errorMessage.value = `Une erreur s'est produite : ${error.message}`;
    console.error('Erreur de récupération des forums :', error);
  }
};

// Charger les forums au montage et à chaque changement de recherche
fetchForums(page.value, searchTerm.value);

const searchForums = () => {
  page.value = 1;
  fetchForums(page.value, searchTerm.value);
};

// Pagination
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
    isLastPage.value = false;
  }
};
</script>

<style scoped>
input[type="text"] {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
h1 {
  text-align: center;
  color: #201f1f;
}

h2 {
  text-decoration: underline;
  color: #201f1f;
}

p {
  color: #201f1f;
}

button {
  padding: 10px 20px;
  margin: 10px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.forum-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.forum-item {
  margin-bottom: 20px;
}

.error-message {
  color: red;
  margin-top: 15px;
  text-align: center;
}
</style>
