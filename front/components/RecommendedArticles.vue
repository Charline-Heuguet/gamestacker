<template>
    <div v-if="recommendedArticles.length" class="recommended-articles mt-8 mb-10">
      <h3 class="text-xl font-semibold mb-4 dark:text-gray-100">Vous aimerez aussi ...</h3>
      <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <li v-for="article in recommendedArticles" :key="article.id" class="recommended-article-item bg-white dark:bg-neutral-800 rounded-md shadow-md overflow-hidden">
          <!-- Image de l'article -->
          <img
            v-if="article.image"
            :src="`http://localhost:8000/images/articles/${article.image}`"
            :alt="article.title"
            class="w-full h-36 object-cover"
          />
          
          <!-- Contenu de la carte -->
          <div class="p-3">
            <!-- Titre de l'article -->
            <h4 class="text-base font-semibold text-gray-800 mb-1 dark:text-white">{{ article.title }}</h4>
            <p class="text-gray-600 text-xs mb-3 dark:text-gray-100">{{ article.content.slice(0, 80) }}...</p>
            
            <!-- Catégories -->
            <div class="categories flex flex-wrap gap-1 mb-3">
              <span
                v-for="category in article.category"
                :key="category.id"
                class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-xs"
              >
                {{ category.name }}
              </span>
            </div>
            
            <!-- Lien vers l'article -->
            <router-link :to="`/article/${article.id}`" class="text-emerald-600 text-sm font-semibold hover:underline">
              Lire la suite
            </router-link>
          </div>
        </li>
      </ul>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  
  const recommendedArticles = ref([]);
  const route = useRoute();
  
  onMounted(async () => {
    const articleId = route.params.id;
    const response = await fetch(`http://localhost:8000/api/article/${articleId}/recommendations`);
    recommendedArticles.value = await response.json();
  });
  </script>
  
  <style scoped>
  /* Optional: Tailwind classes already provide a cohesive design, but you can add specific styles here if needed */
  </style>
  