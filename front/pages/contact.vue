<template>
  <div class="contact-container mw-80 bg-white py-12 px-4 rounded-lg shadow-lg mx-auto my-6 py-12">
    <h1 class="text-3xl font-bold text-emerald-500 mb-8 text-center">Contactez-nous</h1>
    <form @submit.prevent="sendMessage" class="space-y-6">
      <!-- Nom -->
      <div class="form-group">
        <label for="name" class="block text-lg font-semibold text-gray-700 mb-2">Nom :</label>
        <input
          type="text"
          id="name"
          v-model="name"
          required
          class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500 bg-white"
        />
      </div>

      <!-- Email -->
      <div class="form-group">
        <label for="email" class="block text-lg font-semibold text-gray-700 mb-2 bg-white">Email :</label>
        <input
          type="email"
          id="email"
          v-model="email"
          required
          class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500 bg-white"
        />
      </div>

      <!-- Message -->
      <div class="form-group">
        <label for="message" class="block text-lg font-semibold text-gray-700 mb-2">Message :</label>
        <textarea
          id="message"
          v-model="message"
          required
          rows="6"
          class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500 bg-white resize-none"
        ></textarea>
      </div>

      <!-- reCAPTCHA -->
      <div class="form-group">
        <div id="g-recaptcha" class="g-recaptcha" data-sitekey="6LeplX4qAAAAAC_h2Dyjw8WAp9VYoU-uIDmvLNdz"></div>
      </div>

      <!-- Bouton d'envoi avec état de chargement -->
      <button
        type="submit"
        :disabled="isLoading"
        class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 rounded-lg transition duration-200"
      >
        <span v-if="isLoading"><UIcon name="svg-spinners:90-ring-with-bg" class="w-7 h-7" /></span>
        <span v-else>Envoyer</span>
      </button>
    </form>
    <p v-if="statusMessage" class="mt-6 text-center font-medium text-gray-700">{{ statusMessage }}</p>
  </div>
</template>

<script>
import axios from 'axios';
const { $toast } = useNuxtApp();


export default {
  data() {
    return {
      name: '',
      email: '',
      message: '',
      statusMessage: '',
      isLoading: false, // État de chargement
    };
  },
  mounted() {
    // Vérifie si grecaptcha est chargé, puis rend le reCAPTCHA
    if (typeof grecaptcha !== 'undefined') {
      grecaptcha.ready(() => {
        grecaptcha.render('g-recaptcha', {
          sitekey: '6LeplX4qAAAAAC_h2Dyjw8WAp9VYoU-uIDmvLNdz',
        });
      });
    } else {
      $toast.error("reCAPTCHA n'est pas chargé correctement. 😔");
      console.error("reCAPTCHA n'est pas chargé correctement.");
    }
  },
  methods: {
    async sendMessage() {
      this.isLoading = true; // Active le chargement

      // Récupère le jeton reCAPTCHA
      const recaptchaResponse = grecaptcha.getResponse();

      if (!recaptchaResponse) {
        this.isLoading = false;
        $toast.warning('Veuillez valider le reCAPTCHA. 🤖');
        return;
      }

      try {
        // Envoie les données avec le jeton reCAPTCHA au backend
        await axios.post('https://localhost:8000/api/contact', {
          name: this.name,
          email: this.email,
          message: this.message,
          recaptchaToken: recaptchaResponse,
        });

        grecaptcha.reset(); // Réinitialise le reCAPTCHA après l'envoi
        $toast.success(`Message envoyé avec succès ! 🚀Une réponse vous sera donnée dans le délai le plus bref.`);
      } catch (error) {
        console.error("Erreur d'envoi :", error);
        $toast.error("Erreur lors de l'envoi du message. 😔");
      } finally {
        this.isLoading = false; // Désactive le chargement
      }
    },
  },
};
</script>

<style scoped>
.contact-container {
  box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
}

input,
textarea {
    color:black;
}
</style>
