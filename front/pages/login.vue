<template>
  <div class="login-container min-h-screen flex items-center justify-center bg-white px-4 py-12">
    <div v-if="authStore.isAuthenticated" class="text-center max-w-screen-lg">
      <h1 class="text-2xl font-bold text-center text-gray-800 mb-6 leading-relaxed">
        Bienvenue, {{ authStore.user?.pseudo }}, vous pouvez désormais profiter à 100% de notre site !
      </h1>
    </div>
    <div v-else class="w-full max-w-4xl p-8 bg-white rounded-lg shadow-neumorphism">
      <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Connectez-vous</h1>
      <form @submit.prevent="login" class="space-y-6">
        <div class="form-group">
          <label for="email" class="block text-sm font-medium text-gray-600">Email :</label>
          <input type="email" v-model="email" id="email" required class="input-field" />
        </div>
        <div class="form-group">
          <label for="password" class="block text-sm font-medium text-gray-600">Mot de passe :</label>
          <input type="password" v-model="password" id="password" required class="input-field" />
        </div>
        <button type="submit" class="btn" :disabled="isLoading">
          <span v-if="isLoading" class="loader"></span>
          <span v-else>Se connecter</span>
        </button>
        <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
      </form>

      <!-- Bouton pour créer un compte -->
      <div class="mt-6 text-center">
        <p class="text-gray-600 mb-2">Pas de compte ?</p>
        <a href="/inscription" class="create-account-btn">Créer un compte</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const email = ref('');
const password = ref('');
const errorMessage = ref('');
const isLoading = ref(false);
const { $toast } = useNuxtApp();


const login = async () => {
  errorMessage.value = '';
  isLoading.value = true; // Activer le chargement
  try {
    await authStore.login(email.value, password.value);
    $toast.info('Connexion réussie ! Bienvenue sur notre site.');
    router.push('/profile'); // Redirection vers /profile après la connexion réussie
  } catch (error) {
    errorMessage.value = 'Email ou mot de passe incorrect';
    $toast.error("Une erreur s'est produite ! Veuillez vérifier vos identifiants de connexion.");

  } finally {
    isLoading.value = false; // Désactiver le chargement
  }
};

const logout = () => {
  authStore.logout();
  router.push('/login'); // Redirige vers /login après la déconnexion
};
</script>

<style scoped>
/* Style du conteneur de connexion avec effet neumorphisme */
.login-container {
    padding: 24px;
}

.w-full {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 8px 8px 16px #c5c5c5, -8px -8px 16px #ffffff;
}

/* Style du titre */
h1 {
    font-size: 2.5rem;
    color: #333;
}

/* Style des groupes de champs et labels */
.form-group {
    margin-bottom: 15px;
}

label {
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

/* Champs de saisie avec touches de vert */
.input-field {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #f5f5f5;
    color: #333;
    font-size: 14px;
    transition: border-color 0.3s;
}

.input-field:focus {
    border-color: #34d399;
    outline: none;
}

/* Bouton de connexion avec couleur verte */
.btn {
    width: 100%;
    padding: 12px;
    background-color: #34d399;
    color: white;
    font-weight: bold;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn:disabled {
    background-color: #a7f3d0;
    cursor: not-allowed;
}

/* Style pour le loader */
.loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #34d399;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Bouton de création de compte */
.create-account-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #10b981;
    color: white;
    font-weight: bold;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
    text-decoration: none;
}

.create-account-btn:hover {
    background-color: #059669;
}

/* Message d'erreur */
.error {
    color: #f87171;
    text-align: center;
    margin-top: 10px;
}
</style>
