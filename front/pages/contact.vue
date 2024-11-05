<template>
  <div class="contact-container bg-white py-12 px-4 rounded-lg shadow-lg max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-emerald-500 mb-8 text-center">Contactez-nous</h1>
    <form @submit.prevent="sendMessage" class="space-y-6">
      <div class="form-group">
        <label for="name" class="block text-lg font-semibold text-gray-700 mb-2">Nom :</label>
        <input
          type="text"
          id="name"
          v-model="name"
          required
          class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500"
        />
      </div>
      <div class="form-group">
        <label for="email" class="block text-lg font-semibold text-gray-700 mb-2">Email :</label>
        <input
          type="email"
          id="email"
          v-model="email"
          required
          class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500"
        />
      </div>
      <div class="form-group">
        <label for="message" class="block text-lg font-semibold text-gray-700 mb-2">Message :</label>
        <textarea
          id="message"
          v-model="message"
          required
          rows="6"
          class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500 resize-none"
        ></textarea>
      </div>
      <button
        type="submit"
        class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 rounded-lg transition duration-200"
      >
        Envoyer
      </button>
    </form>
    <p v-if="statusMessage" class="mt-6 text-center font-medium text-gray-700">{{ statusMessage }}</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      name: '',
      email: '',
      message: '',
      statusMessage: ''
    };
  },
  methods: {
    async sendMessage() {
      try {
        const response = await axios.post('http://localhost:8000/api/contact', {
          name: this.name,
          email: this.email,
          message: this.message
        });
        this.statusMessage = 'Votre message a bien été envoyé !';
      } catch (error) {
        this.statusMessage = 'Erreur lors de l\'envoi du message.';
      }
    }
  }
};
</script>

<style scoped>
.contact-container {
  box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
}
</style>
