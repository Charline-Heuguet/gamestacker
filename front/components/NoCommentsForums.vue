<template>
    <div class="no-comments-forum sticky top-4">
      <h2 class="text-xl font-semibold mb-4">Ils ont aussi besoin de votre aide !</h2>
      <small>Voici une liste de ticket sans commentaires.</small>
      <div v-if="forums.length">
        <ul>
          <li v-for="forum in forums" :key="forum.id" class="forum-item bg-gray-100 p-4 rounded mb-4 shadow">
            <h3 class="text-lg font-bold">
              <!-- Utilise le router-link pour naviguer vers la page du forum -->
              <router-link :to="`/forum/${forum.id}`" class="text-emerald-500 hover:underline">
                {{ forum.title }}
              </router-link>
            </h3>
            <p class="text-gray-500 text-sm mt-2">
            {{ formatDate(forum.date) }} |
            {{ forum.user?.pseudo || "Anonyme" }}
            </p>
          </li>
        </ul>
      </div>
      <p v-else class="text-gray-500">Aucun forum sans commentaire trouvé.</p>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  
  const forums = ref([]);
  const backendUrl = 'http://localhost:8000';
  
  const fetchNoCommentsForums = async () => {
    try {
      const response = await fetch(`${backendUrl}/api/forum/no-comments`);
      if (!response.ok) throw new Error("Erreur lors de la récupération des forums");
      
      forums.value = await response.json();
    } catch (error) {
      console.error(error);
    }
  };
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("fr-FR", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  };
  
  onMounted(fetchNoCommentsForums);
  </script>
  
  <style scoped>
  .no-comments-forum {
    max-width: 600px;
    margin: 0 auto;
  }
  .forum-item {
    border: 1px solid #ddd;
    padding: 1rem;
  }
  
  small {
    color: green;
    margin-bottom: 1rem;
  }
  </style>
  