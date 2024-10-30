<template>
    <div class="announcement-container p-8">
      <h1 class="text-2xl font-bold mb-6 text-center">Liste des Annonces</h1>
      
      <!-- Barre de recherche -->
      <input
  v-model="searchTerm"
  @input="searchAnnouncements"
  placeholder="Rechercher par #room ou jeu"
  class="w-full p-3 mb-6 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-900 focus:outline-none focus:border-blue-500"
/>

  
      <div v-if="announcements.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="announcement in announcements" :key="announcement.id" class="announcement-card relative bg-gray-800 text-white p-6 rounded-lg shadow-lg">
          
          <!-- Room ID en haut à droite avec espacement -->
          <span class="absolute top-4 right-4 text-blue-300 text-sm font-medium">
            {{ announcement.roomId }}
          </span>
          
          <h2 class="text-xl font-semibold mb-2 mt-8">{{ announcement.title }}</h2>
          <p class="mb-1"><strong class="text-gray-400">Jeu :</strong> {{ announcement.game }}</p>
          <p class="mb-1"><strong class="text-gray-400">Date :</strong> {{ formatDate(announcement.date) }}</p>
          <p class="mb-1"><strong class="text-gray-400">Nb max de joueurs :</strong> {{ announcement.max_nb_players }}</p>
          
          <!-- Bouton pleine largeur en bas de la carte -->
          <button @click="openModal(announcement)" class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
          Rejoindre le salon
        </button>
      </div>
    </div>

    <!-- Modale -->
    <div v-if="showModal" class=" fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="modal bg-gray-900 text-white p-8 rounded-lg shadow-lg max-w-sm w-full">
        <h3 class="text-xl font-semibold mb-4">Confirmer votre action.</h3>
        <p class="mb-6">Voulez-vous rejoindre le salon "{{ selectedAnnouncement.title }}" ?</p>
        <p class="mb-6">Restez respectueux, le créateur du salon aura le droit de vous expulser du salon.</p>
        
        <div class="flex justify-end space-x-4">
          <button @click="confirmJoin" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">Rejoindre</button>
          <button @click="closeModal" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded">Quitter</button>
        </div>
      </div>
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
  
      
      <p v-if="errorMessage" class="text-red-500 text-center mt-4">{{ errorMessage }}</p>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const announcements = ref([]);
  const errorMessage = ref(null);
  const page = ref(1);
  const isLastPage = ref(false);
  const totalItems = ref(0);
  const itemsPerPage = 10;
  const searchTerm = ref(''); 
  const showModal = ref(false);
const selectedAnnouncement = ref(null);

  // Fonction pour ouvrir la modale et sélectionner une annonce
const openModal = (announcement) => {
  selectedAnnouncement.value = announcement;
  showModal.value = true;
};

// Fonction pour fermer la modale
const closeModal = () => {
  showModal.value = false;
  selectedAnnouncement.value = null;
};

// Fonction de confirmation pour rejoindre
const confirmJoin = () => {
  console.log(`Vous avez rejoint le salon: ${selectedAnnouncement.value.title}`);
  closeModal();
};
  
  // Format de la date
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
  };
  
  // Fonction pour récupérer les annonces avec un terme de recherche et une page
  const fetchAnnouncements = async (pageNum = 1, term = '') => {
    try {
      const response = await fetch(`http://localhost:8000/api/announcement?page=${pageNum}&search=${term}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      });
  
      if (!response.ok) {
        throw new Error(`Erreur HTTP ! statut : ${response.status} (${response.statusText})`);
      }
  
      const data = await response.json();
      announcements.value = data.items;
      totalItems.value = data.totalItems;
      isLastPage.value = pageNum * itemsPerPage >= totalItems.value;
    } catch (error) {
      errorMessage.value = `Une erreur s'est produite : ${error.message}`;
    }
  };
  
  // Charger les annonces au montage et à chaque changement de recherche
  fetchAnnouncements(page.value, searchTerm.value);
  
  const searchAnnouncements = () => {
    page.value = 1;
    fetchAnnouncements(page.value, searchTerm.value);
  };
  
  // Pagination
  const nextPage = () => {
    if (!isLastPage.value) {
      page.value += 1;
      fetchAnnouncements(page.value, searchTerm.value);
    }
  };
  
  const prevPage = () => {
    if (page.value > 1) {
      page.value -= 1;
      fetchAnnouncements(page.value, searchTerm.value);
      isLastPage.value = false;
    }
  };
  </script>
  
  <style scoped>
  .announcement-container {
    max-width: 1300px;
    margin: 0 auto;
  }
  
  .announcement-card {
    background-color: #222;
    border: 1px solid #444;
    border-radius: 8px;
  }
  
  .btn-pagination {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .btn-pagination:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  
  .modal {
    max-width: 500px;
  }
  </style>
  