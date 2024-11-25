<template>
    <div class="announcement-container min-h-screen bg-white dark:bg-neutral-900 py-12 px-4">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Liste des Annonces</h1>

        <!-- Bouton pour créer une annonce -->
        <div class="text-center mb-8">
            <button v-if="authStore.isAuthenticated" @click="openCreateModal" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded">
                Créer une annonce
            </button>
            <button v-else @click="showLoginAlert" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                Se connecter pour créer une annonce
            </button>
        </div>

        <!-- Barre de recherche -->
        <input
            v-model="searchTerm"
            @input="searchAnnouncements"
            placeholder="Rechercher par #room ou jeu"
            class="w-full p-3 mb-8 border border-gray-300 rounded-lg shadow-sm bg-white dark:bg-neutral-700 dark:text-white text-gray-900 focus:outline-none focus:border-emerald-500"
        />

        <!-- Liste des annonces -->
        <div v-if="announcements.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="announcement in announcements" :key="announcement.id" class="announcement-card relative bg-gray-100 dark:bg-neutral-800 text-gray-800 p-6 rounded-lg shadow">
                <div class="user-image mb-4">
                    <img 
                        :src="announcement.user.image ? `${backendUrl}/images/users/${announcement.user.image}` : `${backendUrl}/images/users/default.jpg`" 
                        alt="Image de l'utilisateur" 
                        class="w-16 h-16 rounded-full object-cover" 
                    />
                </div>
                <span class="room-id absolute top-4 right-4 text-sm font-medium">
                    {{ announcement.roomId }}
                </span>
                <h2 class="text-xl font-semibold mb-2 mt-8 dark:text-emerald-400">{{ announcement.title }}</h2>
                <p class="mb-1 dark:text-neutral-300"><strong class="text-gray-500 dark:text-neutral-300">Jeu :</strong> {{ announcement.game }}</p>
                <p class="mb-1 dark:text-neutral-300"><strong class="text-gray-500 dark:text-neutral-300">Date :</strong> {{ formatDate(announcement.date) }}</p>
                <p class="mb-1 dark:text-neutral-300"><strong class="text-gray-500 dark:text-neutral-300">Nb max de joueurs :</strong> {{ announcement.max_nb_players }}</p>
                <a :href="`/announcement/${announcement.id}`" class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded text-center block">
                    Voir l'annonce
                </a>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center space-x-4 mt-8">
            <button @click="prevPage" :disabled="page <= 1" class="btn-pagination" :class="{ 'opacity-50 cursor-not-allowed': page <= 1 }">
                Page précédente
            </button>
            <button @click="nextPage" :disabled="isLastPage" class="btn-pagination" :class="{ 'opacity-50 cursor-not-allowed': isLastPage }">
                Page suivante
            </button>
        </div>

        <!-- Message d'erreur -->
        <p v-if="errorMessage" class="text-red-500 text-center mt-4">{{ errorMessage }}</p>

        <!-- Modal de création d'annonce -->
        <div v-if="showCreateModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="modal bg-gray-800 text-white p-8 rounded-lg shadow-lg max-w-4xl w-full">
                <h3 class="text-xl font-semibold mb-4 text-white">Créer une annonce</h3>
                <form @submit.prevent="createAnnouncement">
                    <div class="mb-4">
                        <label for="title" class="block text-gray-300">Titre</label>
                        <input v-model="newAnnouncement.title" type="text" id="title" class="w-full p-2 mt-1 rounded bg-gray-700 text-white" required />
                    </div>
                    <div class="mb-4">
                        <label for="content" class="block text-gray-300">Contenu</label>
                        <textarea v-model="newAnnouncement.content" id="content" class="w-full p-2 mt-1 rounded bg-gray-700 text-white" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="game" class="block text-gray-300">Jeu</label>
                        <input v-model="newAnnouncement.game" type="text" id="game" class="w-full p-2 mt-1 rounded bg-gray-700 text-white" required />
                    </div>
                    <div class="mb-4">
                        <label for="maxPlayers" class="block text-gray-300">Nombre maximum de joueurs</label>
                        <input v-model.number="newAnnouncement.max_nb_players" type="number" id="maxPlayers" class="w-full p-2 mt-1 rounded bg-gray-700 text-white" required />
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
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';  // Importer le store auth pour l'état d'authentification

const authStore = useAuthStore();  // Utiliser authStore pour vérifier l'état de connexion
const announcements = ref([]);
const errorMessage = ref(null);
const page = ref(1);
const isLastPage = ref(false);
const backendUrl = 'https://localhost:8000';
const totalItems = ref(0);
const itemsPerPage = 10;
const searchTerm = ref('');

const showCreateModal = ref(false);
const newAnnouncement = ref({
    title: '',
    content: '',
    game: '',
    max_nb_players: 1
});

// Format de la date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
};

const openCreateModal = () => {
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    newAnnouncement.value = {
        title: '',
        content: '',
        game: '',
        max_nb_players: 1
    };
};

const createAnnouncement = async () => {
    try {
        const response = await fetch(`${backendUrl}/api/announcement/create`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${authStore.token}`  // Envoie du token d'authentification
            },
            body: JSON.stringify(newAnnouncement.value)
        });

        const data = await response.json();
        if (!response.ok) {
            alert(`Erreur : ${data.status}`);
        } else {
            alert('Annonce créée avec succès !');
            closeCreateModal();
            window.location.href = `/announcement/${data.id}`;
        }
    } catch (error) {
        console.error("Erreur lors de la création de l'annonce :", error);
    }
};

// Récupération des annonces
const fetchAnnouncements = async (pageNum = 1, term = '') => {
    try {
        const response = await fetch(`${backendUrl}/api/announcement?page=${pageNum}&search=${term}`, {
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

// Charger les annonces initialement
fetchAnnouncements(page.value, searchTerm.value);

const searchAnnouncements = () => {
    page.value = 1;
    isLastPage.value = false;
    fetchAnnouncements(page.value, searchTerm.value);
};

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

// Afficher une alerte de connexion si l'utilisateur n'est pas authentifié
const showLoginAlert = () => {
    alert("Veuillez vous connecter pour créer une annonce.");
};

</script>

<style scoped>
h1 {
    font-size: 2.5rem;
    background: -webkit-linear-gradient(#08af7f, #aae498);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.announcement-container {
    max-width: 1300px;
    margin: 0 auto;
}

.announcement-card {
    border-radius: 8px;
    /*box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;  Neumorphisme */
}

.room-id {
    color: #10b981;
    text-shadow: 0px 0 6px rgba(16, 185, 129, 0.5);
}

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
</style>
