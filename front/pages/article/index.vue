<template>
  <div>
    <!-- Slider Swiper Full Width -->
    <swiper
      v-if="articles.length > 0"
      :slides-per-view="1"
      loop
      autoplay
      class="w-full h-96"
    >
      <swiper-slide
        v-for="article in articles.slice(0, 3)"
        :key="article.id"
        class="relative flex items-center bg-gray-800 text-white"
      >
        <img
          v-if="article.image"
          :src="`${backendUrl}/images/articles/${article.image}`"
          alt="Image de l'article"
          class="absolute inset-0 w-full h-full object-cover opacity-50"
        />
        <div class="relative z-10 p-6 article-content-swiper">
          <h2 class="title-swiper-big font-bold">{{ article.title }}</h2>
          <p class="mt-2">{{ article.content ? article.content.slice(0, 100) : "Pas de contenu disponible" }}...</p>
          <button
            @click="viewArticle(article.id)"
            class="rounded-3xl mt-4 bg-emerald-500 hover:bg-emerald-700 text-white py-2 px-4"
          >
            Lire l'article
          </button>
        </div>
      </swiper-slide>
    </swiper>

    <!-- Page Content -->
    <div class="page-container flex p-8 gap-8">
      <!-- Liste des Articles -->
      <div class="articles-section flex-1">
        <h1 class="text-3xl font-bold mb-6 text-emerald-500">Liste des Articles</h1>
        
        <!-- Filtre par catégorie pour les articles -->
        <div class="filter-section mb-6">
          <label for="categoryFilter" class="block mb-2 text-emerald-500">Filtrer par catégorie :</label>
          <select id="categoryFilter" v-model="selectedCategory" @change="filterArticles" class="p-2 border rounded">
            <option value="">Toutes les catégories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div v-if="loading" class="text-center text-gray-500">Chargement des articles...</div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div
            v-for="article in paginatedArticles"
            :key="article.id"
            class="bg-white border border-gray-200 rounded-lg shadow-lg"
          >
            <a @click.prevent="viewArticle(article.id)" href="#">
              <img
                v-if="article.image"
                :src="`${backendUrl}/images/articles/${article.image}`"
                alt="Image de l'article"
                class="rounded-t-lg w-full h-36 object-cover"
              />
            </a>

            <div class="p-5">
              <a @click.prevent="viewArticle(article.id)" href="#">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                  {{ article.title }}
                </h5>
              </a>

              <div class="flex gap-2 mb-2">
                <span
                  v-for="cat in article.category"
                  :key="cat.name"
                  class="inline-block bg-emerald-600 rounded-full px-2 py-1 text-sm font-semibold text-white"
                >
                  {{ cat.name }}
                </span>
              </div>

              <p class="mb-3 font-normal text-gray-700">
                {{ article.content ? article.content.slice(0, 250) : "Pas de contenu disponible" }}...
              </p>

              <a
                @click.prevent="viewArticle(article.id)"
                href="#"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-emerald-500 rounded-lg hover:bg-emerald-700"
              >
                Voir l'article
                <svg
                  class="rtl:rotate-180 w-3.5 h-3.5 ms-2"
                  aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 14 10"
                >
                  <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M1 5h12m0 0L9 1m4 4L9 9"
                  />
                </svg>
              </a>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-3 py-2 mx-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 disabled:opacity-50"
          >
            Précédent
          </button>
          <button
            v-for="page in totalPages"
            :key="page"
            @click="currentPage = page"
            :class="['px-3 py-2 mx-1 rounded', page === currentPage ? 'bg-emerald-500 text-white' : 'bg-gray-300 text-gray-800 hover:bg-gray-400']"
          >
            {{ page }}
          </button>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 mx-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 disabled:opacity-50"
          >
            Suivant
          </button>
        </div>
      </div>

      <!-- Section des Annonces - Intégration du composant AnnounceList -->
      <div class="announcements-section w-1/3 hidden md:block">
      <AnnounceList />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AnnounceList from '@/components/AnnounceList.vue'

// Import Swiper components
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/swiper-bundle.css'

const backendUrl = 'http://localhost:8000'
const articles = ref([])
const categories = ref([])
const selectedCategory = ref('')
const loading = ref(true)
const router = useRouter()

const currentPage = ref(1)
const articlesPerPage = 6

// Fonction pour obtenir les articles pour la page courante
const paginatedArticles = computed(() => {
  const start = (currentPage.value - 1) * articlesPerPage
  const end = start + articlesPerPage
  return articles.value.slice(start, end)
})

// Total des pages
const totalPages = computed(() => Math.ceil(articles.value.length / articlesPerPage))

// Pagination navigation
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

// Fonction pour récupérer les articles
const fetchArticles = async (categoryId = '') => {
  loading.value = true
  try {
    const url = categoryId ? `${backendUrl}/api/article?category=${categoryId}` : `${backendUrl}/api/article`
    const response = await fetch(url)
    if (!response.ok) {
      throw new Error(`Erreur lors de la récupération des articles : ${response.statusText}`)
    }
    articles.value = await response.json()
    currentPage.value = 1  // Reset to first page on new fetch
  } catch (error) {
    console.error("Erreur lors de la récupération des articles :", error)
  } finally {
    loading.value = false
  }
}

// Fonction pour récupérer les catégories
const fetchCategories = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/category`)
    if (!response.ok) {
      throw new Error('Erreur lors de la récupération des catégories')
    }
    categories.value = await response.json()
  } catch (error) {
    console.error(error)
  }
}

const filterArticles = () => {
  fetchArticles(selectedCategory.value)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const viewArticle = (id) => {
  router.push(`/article/${id}`)
}

onMounted(() => {
  fetchArticles()
  fetchCategories()
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
.filter-section {
  margin-bottom: 1rem;
}

.title-swiper-big {
  text-transform: uppercase;
  font-size: 2.5rem;
  text-align: left;
}

.article-content-swiper {
      position: absolute;
    bottom: 0;
    max-width: 60%;
}
</style>
