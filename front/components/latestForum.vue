<template>
  <section class="home py-4">
    <div class="title">
      <img src="/medias/icons/chat.svg" alt="deux bulles de discussion">
      <h2 class="text-xl font-bold mb-4">Le Forum</h2>
    </div>
    <p class="text-gray-600 mb-6">
      Besoin d'un coup de pouce pour progresser ? Visite notre forum où joueurs novices et experts échangent
      conseils, astuces, et secrets. Poste ta question ou aide la communauté à résoudre ses énigmes !
    </p>
    <!-- Conteneur du tableau pour fixer la largeur -->
    <div class="table-wrapper">
      <table v-if="latestForumPosts" class="forum-table">
        <thead>
          <tr>
            <th v-for="column in columns" :key="column.key" :class="column.class">{{ column.label }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in forumRows" :key="row.id" @click="handleRowClick(row)">
            <td>{{ row.title }}</td>
            <td>{{ row.content }}</td>
            <td>{{ row.commentCount }}</td>
          </tr>
        </tbody>
      </table>
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
  { key: 'title', label: 'TITRE'},
  { key: 'content', label: 'CONTENU'},
  { key: 'commentCount', label: 'COMMENTAIRES'}
];

// Mapper les données pour inclure le nombre de commentaires
const forumRows = computed(() => {
  return latestForumPosts.value?.map(post => ({
    id: post.id, // Ajoutez l'ID ici
    title: post.title,
    content: post.content,
    commentCount: post.comment.length
  })) || [];
});

// Méthode pour gérer le clic sur une ligne
const handleRowClick = (row) => {
  router.push(`/forum/${row.id}`);
};
</script>

<style scoped>
.title {
  display: flex;
  align-items: end;

  img {
    margin-bottom: 16px;
    width: 50px;
    height: 50px;
    margin-right: 10px;
  }
}

/* Conteneur du tableau */
.table-wrapper {
  width: 100%;
  max-width: 1000px;
  overflow-x: auto;
  margin: 0 auto;
  box-shadow: 10px 5px 5px gray;
}

/* Style global du tableau */
.forum-table {
  border: 1px solid #1F2937 !important;
  
  td {
    padding: 10px !important;
    border: 1px solid #ccc !important;
    background-color: #ffffff;
    color: #1F2937;
  }

  th {
    padding: 10px !important;
    border: 1px solid #ccc !important;
    background-color: #1F2937 !important;
    color: #ccc;
    font-weight: bold !important;
    text-align: left !important;
  }
  tr {
    cursor: pointer;
  }
}

/* Ajouter un style pour les lignes cliquables */
</style>