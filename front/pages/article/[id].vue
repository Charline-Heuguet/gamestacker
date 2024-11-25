<template>
  <div class="article-page p-8">
    <div v-if="loading" class="text-center text-gray-500 dark:text-gray-100">
      Chargement de l'article...
    </div>
    <div v-else-if="article">
      <div
        class="article-container mx-auto text-gray-500 dark:text-gray-100 p-6 rounded-lg shadow-lg"
      >
        <h1 class="text-3xl font-bold mb-4 text-emerald-500">
          {{ article.title }}
        </h1>

        <img
          v-if="article.image"
          :src="`${backendUrl}/images/articles/${article.image}`"
          alt="Image de l'article"
          class="w-full h-64 object-cover rounded mb-4"
        />

        <p class="text-gray-400 dark:text-gray-100 mb-4">
          Date de publication : {{ formatDate(article.date) }}
        </p>

        <div
          v-if="article.category && article.category.length > 0"
          class="mb-4"
        >
          <h3 class="text-lg font-semibold dark:text-gray-100">Catégories :</h3>
          <div class="flex gap-2 mt-2">
            <span
              v-for="cat in article.category"
              :key="cat.name"
              class="inline-block bg-emerald-500 rounded-full px-2 py-1 text-sm font-semibold text-white"
              >{{ cat.name }}</span
            >
          </div>
        </div>

        <p
          v-html="article.content"
          class="text-lg font-semibold text-black dark:text-white"
        ></p>

        <RecommendedArticles />
        <div>
          <AddCommentArticle @commentAdded="fetchArticle" />
        </div>

        <!-- Section des commentaires avec pagination -->
        <div v-if="paginatedComments.length" class="comments-section mt-6">
          <h3 class="text-2xl font-bold text-emerald-500 mb-4">Discussion :</h3>
          <div
            v-for="comment in paginatedComments"
            :key="comment.id"
            class="comment-item bg-emerald-50 p-4 gap-3 rounded-lg mt-4 relative"
            :id="`comment-${comment.id}`"
          >
            <p class="text-gray-500">{{ comment.content }}</p>
            <div class="flex items-center gap-4">
              <button
                @click="upvoteComment(comment.id)"
                :disabled="isCommentUpvoted(comment.id) || isLoading"
                :class="{ upvoted: isCommentUpvoted(comment.id) }"
              >
                <UIcon
                  name="lucide:arrow-big-up"
                  class="w-6 h-6 text-gray-500"
                /><span class="dark:text-gray-500"
                  >Je trouve cela utile ({{ comment.upvote }})</span
                >
                <UIcon
                  v-if="isLoading && currentUpvoteId === comment.id"
                  name="svg-spinners:3-dots-bounce"
                  class="w-6 h-6 ml-2 text-gray-500"
                />
              </button>
              <button
                class="absolute top-5 right-5"
                @click="openReportModal(comment.id)"
              >
                <UIcon
                  name="lucide:message-square-warning"
                  class="w-6 h-6 text-red-500"
                />
              </button>
            </div>

            <div class="flex items-center gap-2">
              <img
                :src="
                  comment.user.image
                    ? `${backendUrl}/images/users/${comment.user.image}`
                    : `${backendUrl}/images/default.jpg`
                "
                :alt="comment.user.pseudo"
                class="w-10 h-10 rounded-full object-cover"
              />
              <p class="text-gray-700 font-medium">{{ comment.user.pseudo }}</p>
            </div>
            <p class="text-sm text-gray-400 mt-2">
              Posté le : {{ formatDate(comment.date) }}
            </p>
          </div>

          <!-- Pagination controls -->
          <div class="flex justify-center mt-4 space-x-2">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="px-4 py-2 bg-emerald-500 text-white rounded"
            >
              Précédent
            </button>
            <button
              v-for="page in totalPages"
              :key="page"
              @click="setPage(page)"
              :class="[
                'px-3 py-2 rounded',
                page === currentPage
                  ? 'bg-emerald-700 text-white'
                  : 'bg-gray-200 text-gray-700',
              ]"
            >
              {{ page }}
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-4 py-2 bg-emerald-500 text-white rounded"
            >
              Suivant
            </button>
          </div>
        </div>

        <div v-else class="text-gray-400 text-center mt-6">
          Aucun commentaire pour cet article.
        </div>
      </div>
    </div>
    <div v-else class="text-center text-red-500">Article non trouvé.</div>

    <!-- Modale de signalement -->
    <div v-if="showReportModal" class="modal-backdrop">
      <div class="modal bg-white dark:bg-neutral-800">
        <h2 class="text-xl font-semibold mb-4 dark:text-white">
          Signaler un commentaire
        </h2>
        <label for="category" class="block mb-2"
          >Sélectionnez la catégorie du signalement :</label
        >
        <select
          v-model="selectedCategory"
          id="category"
          class="p-2 border rounded w-full"
        >
          <option
            v-for="category in categories"
            :key="category.id"
            :value="category.id"
          >
            {{ category.name }}
          </option>
        </select>
        <button
          @click="submitReport"
          class="mt-4 bg-red-500 text-white p-2 mr-5 rounded"
        >
          Envoyer signalement
        </button>
        <button @click="closeReportModal" class="mt-2 text-gray-500">
          Annuler
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import AddCommentArticle from "@/components/AddCommentArticle.vue";
import { useAuthStore } from "@/stores/auth";
import RecommendedArticles from "~/components/RecommendedArticles.vue";

const backendUrl = "http://localhost:8000";
const route = useRoute();
const article = ref(null);
const loading = ref(true);
const authStore = useAuthStore();
const isLoading = ref(false);
const currentUpvoteId = ref(null);
const currentPage = ref(1);
const commentsPerPage = 5;

// État pour le signalement
const showReportModal = ref(false);
const selectedCommentId = ref(null);
const selectedCategory = ref(null);
const categories = ref([]); // Contient les catégories de signalement

// Calcul des commentaires paginés
const paginatedComments = computed(() => {
  if (!article.value || !article.value.comment) return [];
  const start = (currentPage.value - 1) * commentsPerPage;
  return article.value.comment.slice(start, start + commentsPerPage);
});

const totalPages = computed(() => {
  return article.value
    ? Math.ceil(article.value.comment.length / commentsPerPage)
    : 0;
});

const setPage = (page) => {
  currentPage.value = page;
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

// Fonction pour récupérer les détails de l'article
const fetchArticle = async () => {
  try {
    const response = await fetch(
      `${backendUrl}/api/article/${route.params.id}`
    );
    if (!response.ok)
      throw new Error("Erreur lors de la récupération de l'article");
    article.value = await response.json();
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

// Fonction pour vérifier si le commentaire a été upvoté
const isCommentUpvoted = (commentId) => {
  const upvotedComments =
    JSON.parse(localStorage.getItem("upvotedComments")) || [];
  return upvotedComments.includes(commentId);
};

// Fonction pour gérer les upvotes
const upvoteComment = async (commentId) => {
  isLoading.value = true;
  currentUpvoteId.value = commentId;

  try {
    const response = await fetch(
      `${backendUrl}/api/comment/${commentId}/upvote`,
      {
        method: "POST",
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
      }
    );

    if (!response.ok) throw new Error("Erreur lors de l'upvote du commentaire");

    // Ajouter l'ID du commentaire dans le localStorage pour éviter un nouvel upvote
    let upvotedComments =
      JSON.parse(localStorage.getItem("upvotedComments")) || [];
    if (!upvotedComments.includes(commentId)) {
      upvotedComments.push(commentId);
      localStorage.setItem("upvotedComments", JSON.stringify(upvotedComments));
    }

    await fetchArticle(); // Rafraîchit les commentaires après l'upvote
  } catch (error) {
    console.error(error);
  } finally {
    isLoading.value = false;
    currentUpvoteId.value = null;
    $toast.success("Commentaire aimé avec succès !");
  }
};

// Ouvre la modale de signalement
const openReportModal = (commentId) => {
  selectedCommentId.value = commentId;
  showReportModal.value = true;
};

// Ferme la modale de signalement
const closeReportModal = () => {
  showReportModal.value = false;
  selectedCategory.value = null;
  selectedCommentId.value = null;
};

const fetchCategories = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/categories`);
    if (!response.ok)
      throw new Error("Erreur lors de la récupération des catégories");
    categories.value = await response.json();
  } catch (error) {
    console.error("Erreur lors de la récupération des catégories:", error);
  }
};

// Fonction pour envoyer le signalement
const submitReport = async () => {
  if (!selectedCategory.value) {
    alert("Veuillez sélectionner une catégorie de signalement.");
    return;
  }

  try {
    const response = await fetch(
      `${backendUrl}/api/comment/${selectedCommentId.value}/report`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${authStore.token}`,
        },
        body: JSON.stringify({ categoryId: selectedCategory.value }),
      }
    );

    if (!response.ok) throw new Error("Erreur lors de l'envoi du signalement");

    closeReportModal();
    $toast.success("Commentaire signalé avec succès !");
  } catch (error) {
    console.error(error);
    $toast.error("Une erreur s'est produite lors du signalement.");
  }
};

// Fonction pour formater la date
const formatDate = (date) => {
  return new Date(date).toLocaleDateString("fr-FR", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

// Récupérer les données de l'article et les catégories au montage
onMounted(() => {
  fetchArticle();
  fetchCategories();
});
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

.upvoted {
  color: green;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal {
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 400px;
}
</style>
