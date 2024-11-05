<template>
    <div class="announcements-list p-4">
      <h2 class="text-2xl font-bold mb-4 text-emerald-500">Dernières Annonces</h2>
      <p class="text-gray-500">Marre de jouer seul ? <span class="text-emerald-500">Rejoignez un salon !</span></p>
      <div v-if="loading" class="text-center text-gray-500">Chargement des annonces...</div>
      <div v-else-if="announcements.length > 0">
        <div
          v-for="announcement in announcements"
          :key="announcement.id"
          class="announcement-item text-gray-500 p-4 my-4 relative shadow-lg rounded-lg bg-white p-6" 
        >
          <div class="flex items-center mb-4">
            <img
              :src="`${backendUrl}/images/users/${announcement.user.image || 'default.jpg'}`"
              alt="Image de l'utilisateur"
              class="w-12 h-12 rounded-full object-cover mr-4"
            />
            <div>
              <h3 class="text-lg font-semibold">{{ announcement.title }}</h3>
              <p class="text-gray-400">Proposé par : <span class="text-emerald-500">{{ announcement.user.pseudo }}</span></p>
            </div>
          </div>
          <p class="text-gray-400 mb-2">Jeu : {{ announcement.game }}</p>
          <p class="text-gray-400 mb-2">Date : {{ formatDate(announcement.date) }}</p>
          <p v-if="announcement.category.length > 0" class="text-gray-400 mb-2">
            <span
              v-for="cat in announcement.category"
              :key="cat.name"
              class="inline-block bg-emerald-500 gap-3 rounded-full px-2 py-1 text-sm font-semibold text-white mr-2"
            >
              {{ cat.name }}
            </span>
          </p>

          <!-- Lien avec flèche pour voir plus de détails de chaque annonce -->
          <div class="text-right mt-2">
            <a :href="`/announcement/${announcement.id}`" class="text-emerald-500 font-semibold hover:underline flex items-center justify-end">
              <span>Voir l'annonce</span>
              <UIcon name="lucide:arrow-big-right-dash" class="w-7 h-7" />
            </a>
          </div>
          <hr class="mt-4">
        </div>
        
        <!-- Lien pour voir toutes les annonces -->
        <div class="text-center mt-6">
          <a href="/announcement" class="text-emerald-500 font-bold text-lg flex items-center justify-right">
            <span>Voir toutes les annonces</span>
          </a>
        </div>
      </div>
      <div v-else class="text-center text-red-500">Aucune annonce trouvée.</div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// URL de base pour l'API
const backendUrl = 'http://localhost:8000'
const announcements = ref([])
const loading = ref(true)

// Fonction pour récupérer les annonces
const fetchAnnouncements = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/article/announce`)
    if (!response.ok) {
      throw new Error('Erreur lors de la récupération des annonces')
    }
    announcements.value = await response.json()
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

// Fonction pour formater la date
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Charger les annonces lors du montage du composant
onMounted(() => {
  fetchAnnouncements()
})
</script>

<style scoped>
.announcements-list {
  max-width: 400px;
  margin: 0 auto;
}

a:hover {
  color: rgb(16 185 129);
}
</style>
