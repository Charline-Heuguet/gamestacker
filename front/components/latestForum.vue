<template>
  <section class="home py-8">
    <div class="flex items-center mb-6">
      <img src="/medias/icons/chat.svg" alt="deux bulles de discussion" class="w-12 h-12 mr-4">
      <h2 class="text-3xl font-bold text-gray-800">Le Forum</h2>
    </div>
    <p class="text-gray-600 mb-8 text-lg leading-relaxed">
      Besoin d'un coup de pouce pour progresser ? Visite notre forum où joueurs novices et experts échangent
      conseils, astuces, et secrets. Poste ta question ou aide la communauté à résoudre ses énigmes !
    </p>
    <!-- Conteneur du tableau -->
    <div class="overflow-x-auto shadow-lg rounded-lg">
      <table v-if="latestForumPosts" class="min-w-full bg-white">
        <thead class="bg-emerald-500 text-white">
          <tr>
            <th v-for="column in columns" :key="column.key" class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr 
            v-for="row in forumRows" 
            :key="row.id" 
            @click="handleRowClick(row)" 
            class="hover:bg-emerald-50 transition cursor-pointer"
          >
            <td class="px-6 py-4 text-gray-700 border-b">{{ row.title }}</td>
            <td class="px-6 py-4 text-gray-600 border-b">{{ row.content.slice(0,70) + ' ...' }}</td>
            <td class="px-6 py-4 text-gray-600 border-b text-center">{{ row.commentCount }}</td>
          </tr>
        </tbody>
      </table>
      <div v-else class="text-center py-6 text-gray-500">
        Aucun post récent pour le moment.
      </div>
    </div>
  </section>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { computed } from 'vue';

const backUrl = 'https://localhost:8000';
const router = useRouter();

// Appel API pour récupérer les dernières entrées du forum
const { data: latestForumPosts } = await useAsyncData('latest-forum-posts', () => {
  return $fetch(`${backUrl}/api/forum/last-five`);
});

// Configuration des colonnes pour le tableau
const columns = [
  { key: 'title', label: 'Titre' },
  { key: 'content', label: 'Contenu' },
  { key: 'commentCount', label: 'Commentaires' },
];

// Mapper les données pour inclure le nombre de commentaires
const forumRows = computed(() => {
  return latestForumPosts.value?.map(post => ({
    id: post.id,
    title: post.title,
    content: post.content,
    commentCount: post.comment.length,
  })) || [];
});

// Méthode pour gérer le clic sur une ligne
const handleRowClick = (row) => {
  router.push(`/forum/${row.id}`);
};
</script>

<style scoped>
</style>
