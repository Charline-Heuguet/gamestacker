<template>
  <div class="signup-container min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-4xl p-8 bg-white rounded-lg shadow-lg dark:bg-neutral-800">
      <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-gray-400 mb-6">Inscription</h1>
      <form @submit.prevent="handleSignup" class="space-y-6">
        <!-- Première ligne : Email, Pseudo, Discord -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="form-group">
            <label for="email" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Email :</label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              required
              class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-400"
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
              class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-400"
              placeholder="Choisissez un pseudo"
            />
          </div>
          <div class="form-group">
            <label for="discord" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Discord :</label>
            <input
              type="text"
              id="discord"
              v-model="form.discord"
              class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-400"
              placeholder="Votre identifiant Discord"
            />
          </div>
        </div>

        <!-- Deuxième ligne : Âge, Mot de passe -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="form-group">
            <label for="age" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Âge :</label>
            <input
              type="number"
              id="age"
              v-model.number="form.age"
              required
              class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-400"
              placeholder="Entrez votre âge"
            />
          </div>
          <!-- Mdp -->
          <div class="form-group">
            <label for="password" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Mot de passe :</label>
            <input
              type="password"
              id="password"
              v-model="form.password"
              @input="validatePassword"
              required
              class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-400"
              placeholder="Choisissez un mot de passe"
            />
            <span v-if="passwordError" class="text-red-500 text-sm font-medium">{{ passwordError }}</span>

            <!-- conditions pour le mdp -->
            <div class="password-rules mt-2">
              <p class="text-sm font-medium">Le mot de passe doit contenir :</p>
              <ul class="list-none mt-1">
                <li :class="{ 'text-green-500': hasLowercase, 'text-red-500': !hasLowercase }" class="flex items-center">
                  <span v-if="hasLowercase" class="mr-2">✔️</span>
                  <span v-else class="mr-2">✖️</span>
                  Au moins une lettre minuscule
                </li>
                <li :class="{ 'text-green-500': hasUppercase, 'text-red-500': !hasUppercase }" class="flex items-center">
                  <span v-if="hasUppercase" class="mr-2">✔️</span>
                  <span v-else class="mr-2">✖️</span>
                  Au moins une lettre majuscule
                </li>
                <li :class="{ 'text-green-500': hasNumber, 'text-red-500': !hasNumber }" class="flex items-center">
                  <span v-if="hasNumber" class="mr-2">✔️</span>
                  <span v-else class="mr-2">✖️</span>
                  Au moins un chiffre
                </li>
                <li :class="{ 'text-green-500': hasSpecialChar, 'text-red-500': !hasSpecialChar }" class="flex items-center">
                  <span v-if="hasSpecialChar" class="mr-2">✔️</span>
                  <span v-else class="mr-2">✖️</span>
                  Au moins un caractère spécial (@$!%*?&)
                </li>
                <li :class="{ 'text-green-500': isLongEnough, 'text-red-500': !isLongEnough }" class="flex items-center">
                  <span v-if="isLongEnough" class="mr-2">✔️</span>
                  <span v-else class="mr-2">✖️</span>
                  Au moins 8 caractères
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="form-group">
          <label for="description" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Description :</label>
          <textarea
            id="description"
            v-model="form.description"
            class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-400"
            placeholder="Parlez-nous un peu de vous"
          ></textarea>
        </div>

        <!-- Genre -->
        <div class="form-group">
          <label for="gender" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Genre :</label>
          <select id="gender" v-model="form.gender" class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-400">
            <option value="male">Homme</option>
            <option value="female">Femme</option>
            <option value="non-binary">Non-binaire</option>
            <option value="other">Autre</option>
          </select>
        </div>

        <!-- Bouton d'inscription -->
        <button type="submit" class="w-full py-3 bg-teal-400 text-white font-bold rounded-lg hover:bg-teal-500 transition duration-300">
          S'inscrire
        </button>
      </form>

      <!-- Message d'erreur -->
      <p v-if="errorMessage" class="text-red-500 text-center mt-4 font-bold">{{ errorMessage }}</p>
    </div>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue';
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

const passwordError = ref(null);
const errorMessage = ref(null);
const router = useRouter();

const hasLowercase = computed(() => /[a-z]/.test(form.value.password));
const hasUppercase = computed(() => /[A-Z]/.test(form.value.password));
const hasNumber = computed(() => /\d/.test(form.value.password));
const hasSpecialChar = computed(() => /[@$!%*?&]/.test(form.value.password));
const isLongEnough = computed(() => form.value.password.length >= 8);

const validatePassword = () => {
  const password = form.value.password.trim();
  const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
  if (!passwordPattern.test(password)) {
    passwordError.value = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
    return false;
  }
  passwordError.value = null;
  return true;
};

const handleSignup = async () => {
  if (!validatePassword()) {
    return;
  }

  try {
    const response = await $fetch('https://localhost:8000/api/signup', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form.value),
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
