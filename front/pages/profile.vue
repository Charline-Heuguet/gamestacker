<template>
  <div class="h-screen">
    <div class="max-w-5xl mx-auto p-6 bg-gray-100 dark:bg-neutral-800 rounded-lg my-10 shadow-lg">
      <!-- Message pour inviter l'utilisateur non authentifié -->
      <div v-if="profile.error" class="text-center text-gray-700">
        <p>{{ profile.error }}</p>
        <div class="mt-4">
          <router-link to="/login" class="text-emerald-500 hover:underline">Se connecter</router-link> ou 
          <router-link to="/inscription" class="text-emerald-500 hover:underline">Créer un compte</router-link>
        </div>
      </div>

      <!-- Affichage du profil utilisateur si authentifié -->
      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Section d'information utilisateur -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-md p-6 flex flex-col items-center md:items-start">
          <img
            :src="profile.image ? `${backendUrl}/images/users/${profile.image}` : `${backendUrl}/images/default.jpg`"
            alt="Profile Picture"
            class="w-32 h-32 rounded-full object-cover shadow-md"
          />
          <div class="mt-4 text-center md:text-left">
            <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-100">{{ profile.pseudo }}</h1>
            <p class="text-gray-500 dark:text-emerald-500">{{ profile.email }}</p>
          </div>
        </div>

        <!-- Section à propos de l'utilisateur -->
        <div class="md:col-span-2 bg-white dark:bg-neutral-900 rounded-lg shadow-md p-6">
          <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">À propos de moi</h2>
          <p class="text-gray-700 dark:text-gray-100 mt-2">{{ stripHTML(profile.description) || "Aucune description fournie." }}</p>
          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <p class="text-gray-600 dark:text-gray-300"><strong>Âge :</strong> {{ profile.age || 'Non fourni' }}</p>
            <p class="text-gray-600 dark:text-gray-300"><strong>Genre :</strong> {{ profile.gender || 'Non fourni' }}</p>
            <p class="text-gray-600 dark:text-gray-300"><strong>Discord :</strong> {{ profile.discord || 'Non fourni' }}</p>
          </div>
        </div>
      </div>

      <!-- Section des actions ou statistiques -->
      <div v-if="!profile.error" class="mt-8 bg-white dark:bg-neutral-900 rounded-lg shadow-md p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="flex flex-col items-center md:items-start">
          <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Publications</h3>
          <p class="text-gray-500">Nombre d'articles non disponible</p>
        </div>
        <div class="flex flex-col items-center md:items-start">
          <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Commentaires</h3>
          <p class="text-gray-500">Nombre de commentaires non disponible</p>
        </div>
        <div class="flex flex-col items-center md:items-start">
          <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Participation</h3>
          <p class="text-gray-500">Durée de l'adhésion non disponible</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '~/stores/auth' // Chemin de votre store Pinia pour l'authentification

const backendUrl = 'http://localhost:8000'
const profile = ref({})
const authStore = useAuthStore()

// Fonction pour enlever les balises HTML et garder uniquement le texte
const stripHTML = (text) => {
  const div = document.createElement('div');
  div.innerHTML = text;
  return div.textContent || div.innerText || '';
}

// Fonction pour récupérer les informations de l'utilisateur connecté
const fetchProfile = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/profile`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${authStore.token}`, // Utilise le token depuis Pinia
        'Content-Type': 'application/json'
      }
    })

    if (response.status === 401) {
      profile.value = { error: 'Veuillez vous connecter ou créer un compte pour voir votre profil.' }
      return
    }

    if (!response.ok) {
      throw new Error('Erreur lors de la récupération de l\'utilisateur')
    }

    profile.value = await response.json()
  } catch (error) {
    console.error(error.message)
  }
}

// Récupérer les données du profil au montage
onMounted(() => {
  fetchProfile()
})
</script>

<style scoped>
/* Ajoutez des styles personnalisés ici si nécessaire */
</style>
