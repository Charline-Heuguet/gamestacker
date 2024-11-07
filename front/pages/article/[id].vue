<template>
    <div class="article-page p-8">
      <div v-if="loading" class="text-center text-gray-500">Chargement de l'article...</div>
      <div v-else-if="article">
        <div class="article-container mx-auto text-gray-500 p-6 rounded-lg shadow-lg">
          <!-- Titre de l'article -->
          <h1 class="text-3xl font-bold mb-4 text-emerald-500">{{ article.title }}</h1>
  
          <!-- Image de l'article -->
          <img
            v-if="article.image"
            :src="`${backendUrl}/images/articles/${article.image}`"
            alt="Image de l'article"
            class="w-full h-64 object-cover rounded mb-4"
          />
  
          <!-- Détails de l'article -->
          <p class="text-gray-400 mb-4">Date de publication : {{ formatDate(article.date) }}</p>
  
          <!-- Catégories -->
          <div v-if="article.category && article.category.length > 0" class="mb-4">
            <h3 class="text-lg font-semibold">Catégories :</h3>
            <div class="flex gap-2 mt-2">
              <span
                v-for="cat in article.category"
                :key="cat.name"
                class="inline-block bg-emerald-500 rounded-full px-2 py-1 text-sm font-semibold text-white"
              >
                {{ cat.name }}
              </span>
            </div>
          </div>
  
          <!-- Contenu de l'article -->
          <p class="text-gray-600 mb-6">{{ article.content }}</p>
  
          <div>
            <AddCommentArticle @commentAdded="fetchArticle" />
          </div>
  
          <!-- Section des commentaires -->
          <div v-if="article.comment && article.comment.length > 0" class="comments-section mt-6">
            <h3 class="text-2xl font-bold text-emerald-500 mb-4">Discussion:</h3>
            <div
              v-for="comment in article.comment"
              :key="comment.id"
              class="comment-item bg-emerald-50 p-4 gap-3 rounded-lg mt-4"
              :id="`comment-${comment.id}`"
            >
              <p class="text-gray-500">{{ comment.content }}</p>
              <p class="text-sm text-gray-400 mt-2">Posté le : {{ formatDate(comment.date) }}</p>
              <p>{{ comment.user.pseudo }}</p>
              <div>
                <UIcon name="lucide:arrow-big-up" class="w-6 h-6" />{{ comment.upvote }}
                <UIcon name="lucide:arrow-big-down" class="w-6 h-6" />{{ comment.downvote }}
              </div>
            </div>
          </div>
          <div v-else class="text-gray-400 text-center mt-6">Aucun commentaire pour cet article.</div>
        </div>
      </div>
      <div v-else class="text-center text-red-500">Article non trouvé.</div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { useRoute } from 'vue-router'
  import AddCommentArticle from '@/components/AddCommentArticle.vue'
  
  // URL de base pour l'API
  const backendUrl = 'https://localhost:8000'
  const route = useRoute()
  const article = ref(null)
  const loading = ref(true)
  
  // Fonction pour récupérer les détails de l'article
  const fetchArticle = async (newCommentId = null) => {
  try {
    const response = await fetch(`${backendUrl}/api/article/${route.params.id}`)
    if (!response.ok) {
      throw new Error("Erreur lors de la récupération de l'article")
    }
    const articleData = await response.json()

    // Trier les commentaires par nombre de upvotes décroissant
    if (articleData.comment && articleData.comment.length > 0) {
      articleData.comment.sort((a, b) => b.upvote - a.upvote)
    }

    article.value = articleData

    // Scroll vers le nouveau commentaire si l'ID est fourni
    if (newCommentId) {
      await nextTick()
      document.getElementById(`comment-${newCommentId}`).scrollIntoView({ behavior: 'smooth' })
    }
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
  
  // Récupérer les données de l'article au montage
  onMounted(() => {
    fetchArticle()
  })
  </script>
  
  <style scoped>
  .article-page {
    max-width: 1600px;
    margin: 0 auto;
  }
  .comments-section {
    margin-top: 2rem;
  }

  .bg-emerald-50 {
    background-color: rgb(236 253 245);
  }
  </style>
  