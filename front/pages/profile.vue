<template>
  <div class="h-screen">
    <div
      class="max-w-5xl mx-auto p-6 bg-gray-100 dark:bg-neutral-800 rounded-lg my-10 shadow-lg"
    >
      <!-- Message pour inviter l'utilisateur non authentifié -->
      <div v-if="profile.error" class="text-center text-gray-700">
        <p>{{ profile.error }}</p>
        <div class="mt-4">
          <router-link to="/login" class="text-emerald-500 hover:underline"
            >Se connecter</router-link
          >
          ou
          <router-link
            to="/inscription"
            class="text-emerald-500 hover:underline"
            >Créer un compte</router-link
          >
        </div>
      </div>

      <!-- Affichage du profil utilisateur si authentifié -->
      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Section d'information utilisateur -->
        <div
          class="bg-white dark:bg-neutral-900 rounded-lg shadow-md p-6 flex flex-col items-center md:items-start"
        >
          <img
            :src="
              profile.image
                ? `${backendUrl}/images/users/${profile.image}`
                : `${backendUrl}/images/default.jpg`
            "
            alt="Profile Picture"
            class="w-32 h-32 rounded-full object-cover shadow-md"
          />
          <div class="mt-4 text-center md:text-left">
            <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-100">
              {{ profile.pseudo }}
            </h1>
            <p class="text-gray-500 dark:text-emerald-500">
              {{ profile.email }}
            </p>
          </div>
        </div>

        <!-- Section à propos de l'utilisateur -->
        <div
          class="md:col-span-2 bg-white dark:bg-neutral-900 rounded-lg shadow-md p-6"
        >
          <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            À propos de moi
          </h2>
          <p class="text-gray-700 dark:text-gray-100 mt-2">
            {{
              stripHTML(profile.description) || "Aucune description fournie."
            }}
          </p>
          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <p class="text-gray-600 dark:text-gray-300">
              <strong>Âge :</strong> {{ profile.age || "Non fourni" }}
            </p>
            <p class="text-gray-600 dark:text-gray-300">
              <strong>Genre :</strong> {{ profile.gender || "Non fourni" }}
            </p>
            <p class="text-gray-600 dark:text-gray-300">
              <strong>Discord :</strong> {{ profile.discord || "Non fourni" }}
            </p>
          </div>
        </div>
      </div>

      <!-- Section des actions ou statistiques -->
      <div
        v-if="!profile.error"
        class="mt-8 bg-white dark:bg-neutral-900 rounded-lg shadow-md p-6 grid grid-cols-1 md:grid-cols-3 gap-6 h-fit"
      >
        <!-- Section des annonces publiées -->
        <div v-if="announcements.length > 0">
          <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
            Mes Annonces
          </h3>
          <ul>
            <li
              v-for="(announcement, index) in announcements"
              :key="index"
              class="text-gray-600 mb-4"
            >
              <router-link
                :to="`/announcement/${announcement.id}`"
                class="text-emerald-500 hover:underline"
              >
                {{ announcement.title }}
              </router-link>
            </li>
          </ul>
        </div>
        <div v-else>
          <p class="text-gray-500">Aucune annonce trouvée</p>
        </div>

        <!-- Section des articles et forums commentés -->
        <div v-if="commentedItems.length > 0">
          <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
            Articles et Forums Commentés
          </h3>
          <ul>
            <li
              v-for="(item, index) in commentedItems"
              :key="index"
              class="text-gray-600 mb-4"
            >
              <span v-if="item.article_id">
                <router-link
                  :to="`/article/${item.article_id}`"
                  class="text-emerald-500 hover:underline"
                >
                  Article : {{ item.article_title }}
                </router-link>
              </span>
              <span v-if="item.forum_id">
                <router-link
                  :to="`/forum/${item.forum_id}`"
                  class="text-emerald-500 hover:underline"
                >
                  Forum : {{ item.forum_title }}
                </router-link>
              </span>
            </li>
          </ul>
        </div>
        <div v-else>
          <p class="text-gray-500">Aucun article ou forum commenté trouvé</p>
        </div>
        <div class="mt-6 flex justify-center md:justify-start">
          <button
            @click="toggleEditMode"
            class="bg-emerald-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-emerald-600 h-fit"
          >
            Modifier mon profil
          </button>
        </div>
      </div>
    </div>

    <!-- Modale pour modifier le profil -->
    <div
      v-if="editMode"
      class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
    >
      <div
        class="bg-white dark:bg-neutral-900 p-6 rounded-lg shadow-lg max-w-lg w-full h-fit"
      >
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
          Modifier mon profil
        </h3>
        <form @submit.prevent="updateProfile">
          <div class="mb-4">
            <label for="pseudo" class="block text-gray-700 dark:text-gray-300"
              >Pseudo</label
            >
            <input
              id="pseudo"
              v-model="form.pseudo"
              type="text"
              class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-800 dark:text-gray-100"
            />
          </div>
          <div class="mb-4">
            <label
              for="description"
              class="block text-gray-700 dark:text-gray-300"
              >Description</label
            >
            <textarea
              id="description"
              v-model="form.description"
              class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-800 dark:text-gray-100"
            ></textarea>
          </div>
          <div class="mb-4">
            <label for="age" class="block text-gray-700 dark:text-gray-300"
              >Âge</label
            >
            <input
              id="age"
              v-model="form.age"
              type="number"
              class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-800 dark:text-gray-100"
            />
          </div>
          <div class="mb-4">
            <label for="gender" class="block text-gray-700 dark:text-gray-300"
              >Genre</label
            >
            <input
              id="gender"
              v-model="form.gender"
              type="text"
              class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-800 dark:text-gray-100"
            />
          </div>
          <div class="mb-4">
            <label for="discord" class="block text-gray-700 dark:text-gray-300"
              >Discord</label
            >
            <input
              id="discord"
              v-model="form.discord"
              type="text"
              class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-800 dark:text-gray-100"
            />
          </div>
          <div class="flex justify-end">
            <button
              type="button"
              @click="toggleEditMode"
              class="bg-red-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-600 mr-2"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="bg-emerald-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-emerald-600 h-fit"
            >
              Sauvegarder
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "~/stores/auth";

const backendUrl = "http://localhost:8000"; // URL de l'API backend
const profile = ref({}); // Données de profil utilisateur
const announcements = ref([]); // Annonces publiées par l'utilisateur
const commentedItems = ref([]); // Articles et forums commentés
const apiError = ref(null); // Gestion des erreurs API
const authStore = useAuthStore(); // Authentification via Pinia
const editMode = ref(false); // Mode édition du profil
const form = ref({}); // Données du formulaire pour modifier le profil

const stripHTML = (text) => {
  const div = document.createElement("div");
  div.innerHTML = text;
  return div.textContent || div.innerText || "";
};

const toggleEditMode = () => {
  editMode.value = !editMode.value;
  if (editMode.value) {
    form.value = {
      pseudo: profile.value.pseudo || "",
      description: profile.value.description || "",
      age: profile.value.age || null,
      gender: profile.value.gender || "",
      discord: profile.value.discord || "",
    };
  } else {
    form.value = {};
  }
};

const fetchProfile = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/profile`, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${authStore.token}`,
        "Content-Type": "application/json",
      },
    });
    if (!response.ok)
      throw new Error("Erreur lors de la récupération du profil");
    profile.value = await response.json();
  } catch (error) {
    apiError.value = error.message;
    console.error(error.message);
  }
};

const updateProfile = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/profile/update`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${authStore.token}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(form.value),
    });
    if (!response.ok)
      throw new Error("Erreur lors de la mise à jour du profil");
    profile.value = await response.json();
    editMode.value = false;
    alert("Profil mis à jour avec succès !");
    location.reload(); // Recharge la page
  } catch (error) {
    console.error("Erreur :", error.message);
    alert("Une erreur s'est produite lors de la mise à jour.");
  }
};

const fetchUserAnnouncements = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/profile/announcements`, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${authStore.token}`,
        "Content-Type": "application/json",
      },
    });
    if (!response.ok)
      throw new Error("Erreur lors de la récupération des annonces");
    announcements.value = await response.json();
  } catch (error) {
    apiError.value = error.message;
    console.error(error.message);
  }
};

const fetchCommentedItems = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/profile/comments`, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${authStore.token}`,
        "Content-Type": "application/json",
      },
    });
    if (!response.ok)
      throw new Error(
        "Erreur lors de la récupération des articles/forums commentés"
      );
    commentedItems.value = await response.json();
  } catch (error) {
    apiError.value = error.message;
    console.error(error.message);
  }
};

onMounted(() => {
  if (!authStore.token) {
    apiError.value = "Vous devez être connecté pour accéder à cette page.";
    return;
  }
  fetchProfile();
  fetchUserAnnouncements();
  fetchCommentedItems();
});
</script>

<style scoped>
/* Ajoute un effet de fondu pour la modale */
</style>
