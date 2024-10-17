<template>
    <div class="signup-container">
      <h1>Inscription</h1>
      <form @submit.prevent="handleSignup">
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" id="email" v-model="form.email" required />
        </div>
  
        <div class="form-group">
          <label for="pseudo">Pseudo:</label>
          <input type="text" id="pseudo" v-model="form.pseudo" required />
        </div>
  
        <div class="form-group">
          <label for="age">Âge:</label>
          <input type="number" id="age" v-model.number="form.age" required />
        </div>
  
        <div class="form-group">
          <label for="password">Mot de passe:</label>
          <input type="password" id="password" v-model="form.password" required />
        </div>
  
        <div class="form-group">
          <label for="description">Description:</label>
          <textarea id="description" v-model="form.description"></textarea>
        </div>
  
        <div class="form-group">
          <label for="discord">Discord:</label>
          <input type="text" id="discord" v-model="form.discord" />
        </div>
  
        <div class="form-group">
          <label for="gender">Genre:</label>
          <select id="gender" v-model="form.gender">
            <option value="male">Homme</option>
            <option value="female">Femme</option>
            <option value="non-binary">Non-binaire</option>
            <option value="other">Autre</option>
          </select>
        </div>
  
        <button type="submit">S'inscrire</button>
      </form>
  
      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
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
        // Redirection après succès, par exemple vers la page de connexion
        router.push('/login');
      }
    } catch (error) {
      errorMessage.value = "Une erreur s'est produite lors de l'inscription.";
    }
  };
  </script>
  
  <style scoped>
  h1, button{
    color: #201f1f;
    text-align: center;
  }
  label, input {
    display: block;
    color: #ccc;
  }
  .signup-container {
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  .error-message {
    color: red;
    margin-top: 15px;
  }
  </style>
  