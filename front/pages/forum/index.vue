<template>
    <div class="forum-container min-h-screen bg-white py-12 px-4">
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
                
                <!-- Lien vers la page de détails du forum sans router-link -->
                <a v-if="forum.id" :href="`/forum/${forum.id}`" class="text-xl font-semibold text-emerald-600 underline mb-2">
                    {{ forum.title }}
                </a>
                
                <!-- Si l'ID est manquant, on affiche un message d'erreur pour diagnostiquer -->
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

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

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

        // Vérification si chaque élément contient un ID
        forums.value = data.items.map((forum) => {
            if (!forum.id) {
                console.warn("Forum ID manquant:", forum);
            }
            return forum;
        });

        totalItems.value = data.totalItems;
        isLastPage.value = pageNum * itemsPerPage >= totalItems.value;
    } catch (error) {
        errorMessage.value = `Une erreur s'est produite : ${error.message}`;
        console.error('Erreur de récupération des forums :', error);
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
        isLastPage.value = false;
    }
};
</script>

<style scoped>
/* Style général */
.forum-container {
    max-width: 900px;
    margin: 0 auto;
}

/* Effet de Neumorphisme sur les éléments */
.shadow-neumorphism {
    box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
}

/* Style du titre */
h1 {
    font-size: 2.5rem;
    background: -webkit-linear-gradient(#08af7f, #aae498);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Style des champs de saisie */
input[type="text"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

/* Style des items de forum */
.forum-item {
    background-color: #f8fafc;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
}

/* Lien de titre du forum */
.forum-item a {
    color: #34d399;
    font-weight: bold;
    text-decoration: none;
    transition: color 0.3s;
}

.forum-item a:hover {
    color: #059669; /* Vert émeraude foncé au survol */
}

/* Style des boutons de pagination */
.btn-pagination {
    padding: 10px 20px;
    background-color: #34d399; /* Vert émeraude */
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
</style>
