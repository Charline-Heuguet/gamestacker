<template>
    <div class="signup-container min-h-screen flex items-center justify-center px-4 py-12">
      <div class="w-full max-w-4xl p-8 bg-white rounded-lg shadow-lg dark:bg-neutral-800">
        <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-gray-400 mb-6">Inscription</h1>
        <form @submit.prevent="handleSignup" class="space-y-6">
          <!-- Email, Pseudo, Discord -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group">
              <label for="email" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Email :</label>
              <input
                type="email"
                id="email"
                v-model="form.email"
                required
                class="input-field"
                placeholder="Entrez votre email"
              />
            </div>
            <div class="form-group">
              <label for="pseudo" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Pseudo :</label>
              <input
                type="text"
                id="pseudo"
                v-model="form.pseudo"
                required
                class="input-field"
                placeholder="Choisissez un pseudo"
              />
            </div>
            <div class="form-group">
              <label for="discord" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Discord :</label>
              <input
                type="text"
                id="discord"
                v-model="form.discord"
                class="input-field"
                placeholder="Votre identifiant Discord"
              />
            </div>
          </div>
  
          <!-- Âge, Mot de passe -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
              <label for="age" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Âge :</label>
              <input
                type="number"
                id="age"
                v-model.number="form.age"
                required
                class="input-field"
                placeholder="Entrez votre âge"
              />
            </div>
            <div class="form-group">
              <label for="password" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Mot de passe :</label>
              <input
                type="password"
                id="password"
                v-model="form.password"
                required
                class="input-field"
                placeholder="Choisissez un mot de passe"
              />
            </div>
          </div>
  
          <!-- Description -->
          <div class="form-group">
            <label for="description" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Description :</label>
            <textarea
              id="description"
              v-model="form.description"
              class="input-field"
              placeholder="Parlez-nous un peu de vous"
            ></textarea>
          </div>
  
          <!-- Genre -->
          <div class="form-group">
            <label for="gender" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Genre :</label>
            <select id="gender" v-model="form.gender" class="input-field">
              <option value="male">Homme</option>
              <option value="female">Femme</option>
              <option value="non-binary">Non-binaire</option>
              <option value="other">Autre</option>
            </select>
          </div>
  
          <button type="submit" class="btn">S'inscrire</button>
        </form>
  
        <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  
  const form = ref({
    email: '',
    pseudo: '',
    age: null,
    password: '',
    description: '',
    discord: '',
    gender: 'other',
  });
  
  const errorMessage = ref(null);
  const router = useRouter();
  
  const handleSignup = async () => {
    try {
      const response = await $fetch('https://localhost:8000/api/signup', {
        method: 'POST',
        body: form.value,
      });
  
      if (response.error) {
        errorMessage.value = response.error;
      } else {
        router.push('/login');
      }
    } catch (error) {
      errorMessage.value = "Une erreur s'est produite lors de l'inscription.";
    }
  };
  </script>
  
  <style scoped>
  .signup-container {
    font-family: 'Poppins', sans-serif;
  }
  
  .input-field {
    width: 100%;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background-color: #f9fafb;
    color: #374151;
    font-size: 14px;
    transition: all 0.3s;
  }
  
  .input-field:focus {
    outline: none;
    border-color: #34d399;
    box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.4);
  }
  
  .input-field.dark {
    background-color: #374151;
    color: #f3f4f6;
  }
  
  /* Bouton d'inscription */
  .btn {
    width: 100%;
    padding: 14px;
    background-color: #34d399;
    color: white;
    font-weight: bold;
    border-radius: 8px;
    transition: background-color 0.3s;
  }
  
  .btn:hover {
    background-color: #059669;
  }
  
  .error-message {
    color: #f87171;
    text-align: center;
    margin-top: 15px;
    font-weight: bold;
  }
  </style>
  