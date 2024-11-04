<template>
    <div class="page-container flex p-8 gap-8">
      <!-- Liste des Articles -->
      <div class="articles-section flex-1">
        <h1 class="text-3xl font-bold mb-6 text-emerald-500">Liste des Articles</h1>
        <div v-if="loading" class="text-center text-gray-500">Chargement des articles...</div>
        <div v-else-if="articles && articles.length > 0">
          <div
            v-for="article in articles"
            :key="article.id"
            class="article-item shadow-neumorphism text-white p-6 rounded-lg shadow-lg mb-4 relative"
          >
            <!-- Catégories en haut à gauche de l'image -->
            <div class="flex gap-2 absolute top-2 left-2">
              <span
                v-for="cat in article.category"
                :key="cat.name"
                class="inline-block bg-emerald-600 rounded-full px-2 py-1 text-sm font-semibold text-white m-2"
              >
                {{ cat.name }}
              </span>
            </div>
  
            <img
              v-if="article.image"
              :src="`${backendUrl}/images/articles/${article.image}`"
              alt="Image de l'article"
              class="w-3/4 h-36 object-cover rounded mb-4 mx-auto"
            />
  
            <h2 class="text-2xl font-bold mb-2 text-gray-500">{{ article.title }}</h2>
            <p class="text-gray-400 mb-2">Date : {{ formatDate(article.date) }}</p>
  
            <p>{{ article.content }}</p>
  
            <!-- Bouton "Voir l'article" -->
            <div class="text-center mt-4">
              <button
                @click="viewArticle(article.id)"
                class="bg-emerald-500 hover:bg-emerald-700 w-full text-white font-semibold py-2 px-4 rounded"
              >
                Voir l'article
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-red-500">Aucun article trouvé.</div>
      </div>
  
      <!-- Section des Annonces - Intégration du composant AnnounceList -->
      <div class="announcements-section w-1/3">
        <AnnounceList />
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import AnnounceList from '@/components/AnnounceList.vue' // Import du composant
  
  // URL de base pour l'API
  const backendUrl = 'http://localhost:8000'
  const articles = ref([])
  const loading = ref(true)
  const router = useRouter()
  
  // Fonction pour récupérer les articles
  const fetchArticles = async () => {
    try {
      const response = await fetch(`${backendUrl}/api/article/`)
      if (!response.ok) {
        throw new Error('Erreur lors de la récupération des articles')
      }
      articles.value = await response.json()
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
  
  // Fonction pour naviguer vers l'article
  const viewArticle = (id) => {
    router.push(`/article/${id}`)
  }
  
  // Récupérer les articles au montage
  onMounted(() => {
    fetchArticles()
  })
  </script>
  
  <style scoped>
  .page-container {
    display: flex;
    gap: 2rem;
  }
  .articles-section {
    flex: 2;
  }
  .article-item {
    max-width: 1000px;
    position: relative;
  }
  .shadow-neumorphism {
    box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
  }
  .announcements-section {
    flex: 1;
    margin-top: 2rem;
  }
  </style>
  