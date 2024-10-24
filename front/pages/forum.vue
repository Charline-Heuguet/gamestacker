<template>
    <div class="forum-container">
      <h1>Liste des Forums</h1>
      <div v-if="forums.length">
        <div v-for="forum in forums" :key="forum.id" class="forum-item">
          <h2>{{ forum.title }}</h2>
          <p>{{ forum.content }}</p>
          <p><strong>Date :</strong> {{ formatDate(forum.date) }}</p>
          <p><strong>Utilisateur :</strong> {{ forum.user ? forum.user.pseudo : 'Anonyme' }}</p>
          <hr />
        </div>
      </div>
      <p v-else>Aucun forum disponible pour le moment.</p>
      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';


  const forums = ref([]);
  const errorMessage = ref(null);
  const router = useRouter();
  
  const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
  };
  
  // Fonction pour récupérer les forums
  const fetchForums = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/forum', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error(`Erreur HTTP ! statut : ${response.status} (${response.statusText})`);
    }

    forums.value = await response.json();
  } catch (error) {
    errorMessage.value = `Une erreur s'est produite : ${error.message}`;
    console.error('Erreur de récupération des forums :', error);
  }
};

fetchForums();
  </script>
  
  <style scoped>
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
  