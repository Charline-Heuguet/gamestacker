<template>
  <div class="max-w-5xl mx-auto p-6 bg-gray-100 rounded-lg mt-10 shadow-lg">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Section d'information utilisateur -->
      <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center md:items-start">
        <img
  :src="authStore.user.image"
  alt="Profile Picture"
  class="w-32 h-32 rounded-full object-cover shadow-md"
/>


        <div class="mt-4 text-center md:text-left">
          <h1 class="text-3xl font-semibold text-gray-800">{{ authStore.user?.pseudo }}</h1>
          <p class="text-gray-500">{{ authStore.user?.email }}</p>
        </div>
      </div>

      <!-- Section à propos de l'utilisateur -->
      <div class="md:col-span-2 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800">À propos de moi</h2>
        <p class="text-gray-700 mt-2">{{ authStore.user?.description || "Aucune description fournie." }}</p>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <p class="text-gray-600"><strong>Âge :</strong> {{ authStore.user?.age || 'Non fourni' }}</p>
          <p class="text-gray-600"><strong>Genre :</strong> {{ authStore.user?.gender || 'Non fourni' }}</p>
          <p class="text-gray-600"><strong>Discord :</strong> {{ authStore.user?.discord || 'Non fourni' }}</p>
        </div>
      </div>
    </div>

    <!-- Section des actions ou statistiques -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="flex flex-col items-center md:items-start">
        <h3 class="text-xl font-semibold text-gray-800">Publications</h3>
        <p class="text-gray-500">{{ articlesCount }} articles publiés</p>
      </div>
      <div class="flex flex-col items-center md:items-start">
        <h3 class="text-xl font-semibold text-gray-800">Commentaires</h3>
        <p class="text-gray-500">{{ commentsCount }} commentaires postés</p>
      </div>
      <div class="flex flex-col items-center md:items-start">
        <h3 class="text-xl font-semibold text-gray-800">Participation</h3>
        <p class="text-gray-500">Membre depuis {{ membershipDuration }} jours</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

// URL de base pour l'API
const backendUrl = 'http://localhost:8000';
const authStore = useAuthStore();

// Variables pour stocker les données dynamiques
const articlesCount = ref(0);
const commentsCount = ref(0);
const membershipDuration = ref(0);

// Fonction pour calculer la durée d'adhésion en jours
const calculateMembershipDuration = (createdAt) => {
  const joinDate = new Date(createdAt);
  const currentDate = new Date();
  const differenceInTime = currentDate - joinDate;
  const differenceInDays = Math.floor(differenceInTime / (1000 * 60 * 60 * 24));
  return differenceInDays;
};

// Récupération des données dynamiques lors du montage du composant
onMounted(async () => {
  try {
    // Récupérer le nombre d'articles
    const articlesResponse = await fetch(`${backendUrl}/api/user/${authStore.user.id}/articles`);
    const articlesData = await articlesResponse.json();
    articlesCount.value = articlesData.count;

    // Récupérer le nombre de commentaires
    const commentsResponse = await fetch(`${backendUrl}/api/user/${authStore.user.id}/comments`);
    const commentsData = await commentsResponse.json();
    commentsCount.value = commentsData.count;

    // Calculer la durée d'adhésion
    membershipDuration.value = calculateMembershipDuration(authStore.user.createdAt);
  } catch (error) {
  }
});
</script>

<style scoped>
/* Ajoutez des styles personnalisés ici si nécessaire */
</style>
