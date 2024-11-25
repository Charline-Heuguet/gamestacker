<template>
  <!-- Bannière et message de bienvenue -->
  <div 
  class="w-full mb-8 relative shadow-lg banner bg-cover bg-center flex flex-col items-start justify-center px-6 md:px-12 py-16 text-center md:text-left"
  :style="{ backgroundImage: `url('https://i.ibb.co/gSdyxm3/codmobile-hero2.jpg')`, backgroundColor: 'rgba(0, 0, 0, 0.7)', backgroundBlendMode: 'overlay' }"
>
  <h1 class="text-2xl md:text-4xl font-bold text-emerald-500 mb-4">
      Bienvenue sur le forum des passionnés de jeux vidéo
  </h1>
  <p class="text-base md:text-lg text-gray-100 max-w-2xl">
      Posez vos questions, partagez vos expériences et trouvez des partenaires de jeu.
  </p>
  <!-- Bouton pour créer une nouvelle discussion -->
  <button v-if="authStore.isAuthenticated" @click="openCreateModal" class="mt-4 bg-emerald-500 text-white font-semibold py-2 px-4 rounded-full hover:bg-emerald-600">
      Commencer une discussion
  </button>
  <button v-else @click="showLoginAlert" class="mt-4 bg-gray-500 text-white font-semibold py-2 px-4 rounded-full hover:bg-gray-600">
      Se connecter pour créer une discussion
  </button>
</div>


  <!-- Modale de création de forum -->
  <div v-if="showCreateModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="modal bg-gray-800 text-white p-8 rounded-lg shadow-lg max-w-4xl w-full">
          <h3 class="text-xl font-semibold mb-4 text-white">Créer une discussion</h3>
          <form @submit.prevent="createForum">
              <div class="mb-4">
                  <label for="title" class="block text-gray-300">Titre</label>
                  <input v-model="newForum.title" type="text" id="title" class="w-full p-2 mt-1 rounded bg-gray-700 text-white" required />
              </div>
              <div class="mb-4">
                  <label for="content" class="block text-gray-300">Contenu</label>
                  <textarea v-model="newForum.content" id="content" class="w-full p-2 mt-1 rounded bg-gray-700 text-white" required></textarea>
              </div>
              <div class="flex justify-end space-x-4">
                  <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded">
                      Créer
                  </button>
                  <button @click="closeCreateModal" type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                      Annuler
                  </button>
              </div>
          </form>
      </div>
  </div>

  <!-- Contenu principal (Forums) -->
  <div class="forum-container bg-white py-12 px-4 rounded-lg mw-80 w-full dark:bg-neutral-900 ">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8 dark:text-emerald-400">Liste des Forums</h1>

    <!-- Barre de recherche -->
    <input
      v-model="searchTerm"
      @input="searchForums"
      placeholder="Rechercher un forum"
      class="w-full p-3 mb-8 border border-gray-300 rounded-lg shadow-sm bg-white dark:bg-neutral-800 text-gray-900 focus:outline-none focus:border-emerald-500"
    />
    <div v-if="isLoading" class="text-center text-xl text-emerald-500 mt-8">Un peu de patience ! Ca arrive <UIcon name="svg-spinners:90-ring-with-bg" class="w-6 h-6" />
    </div>

    <div v-if="forums.length" class="space-y-8">
      <div v-for="forum in forums" :key="forum.id" class="forum-item bg-gray-100 p-6 rounded-lg shadow-lg dark:bg-neutral-800">
        <a v-if="forum.id" :href="`/forum/${forum.id}`" class="text-xl font-semibold text-emerald-600 underline mb-2">
          {{ forum.title }}
        </a>
        <p v-else class="text-red-500">Erreur : ID manquant pour cet article.</p>
        <p class="text-gray-700 dark:text-gray-100 mb-2">{{ forum.content }}</p>
        <p class="text-gray-600 dark:text-gray-50"><strong>Date :</strong> {{ formatDate(forum.date) }}</p>
        <hr class="my-4 border-gray-300 dark:border-gray-700" />
        <p class="text-neutral-600 dark:text-emerald-800"><strong> {{ forum.user ? forum.user.pseudo : 'Anonyme' }}</strong></p>

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
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();  // Accéder au store d'authentification
const forums = ref([]);
const errorMessage = ref(null);
const page = ref(1);
const isLastPage = ref(false);
const totalItems = ref(0);
const itemsPerPage = 10;
const searchTerm = ref('');
const isLoading = ref(false);  // Variable de chargement
const { $toast } = useNuxtApp();


const showCreateModal = ref(false);
const newForum = ref({
    title: '',
    content: ''
});

// Format de date
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Ouvrir/fermer la modale de création
const openCreateModal = () => {
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    newForum.value = {
        title: '',
        content: ''
    };
};

// Afficher une alerte si l'utilisateur n'est pas connecté
const showLoginAlert = () => {
    $toast.warning("Veuillez vous connecter pour créer une discussion.");
};

// Envoyer la requête pour créer un nouveau forum
const createForum = async () => {
    try {
        const response = await fetch('https://localhost:8000/api/forum/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${authStore.token}`
            },
            body: JSON.stringify(newForum.value)
        });

        const data = await response.json();
        if (!response.ok) {
            $toast.error("Une erreur est survenue lors de la création de la discussion.");
        } else {
            closeCreateModal();
            $toast.success("Ticket créé avec succès !");
            fetchForums();  // Rafraîchit la liste des forums
        }
    } catch (error) {
        console.error("Erreur lors de la création de la discussion :", error);
    }
};

// Récupération des forums
const fetchForums = async (pageNum = 1, term = '') => {
  isLoading.value = true;  // Début du chargement
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
  } finally {
    isLoading.value = false;  // Fin du chargement
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
  padding: 15vh;
   
}

@media (max-width: 768px) {
    .banner {
      padding: 5vh;
    }
}
</style>
