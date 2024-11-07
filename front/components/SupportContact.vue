<template>
  <div>
    <!-- Icone flottante pour le support -->
    <div class="support-icon" @click="toggleModal">
      ?
    </div>

    <!-- Modale de contact pour le support -->
    <div v-if="showModal" class="modal-overlay" @click="toggleModal">
      <div class="modal-content" @click.stop>
        <h2 class="modal-title">Contact Support</h2>
        <form @submit.prevent="sendSupportMessage" class="space-y-4">
          <div class="form-group">
            <label for="name" class="block text-lg font-semibold">Nom :</label>
            <input
              type="text"
              id="name"
              v-model="name"
              required
              class="input-field"
            />
          </div>
          <div class="form-group">
            <label for="email" class="block text-lg font-semibold">Email :</label>
            <input
              type="email"
              id="email"
              v-model="email"
              required
              class="input-field"
            />
          </div>
          <div class="form-group">
            <label for="message" class="block text-lg font-semibold">Message :</label>
            <textarea
              id="message"
              v-model="message"
              required
              rows="4"
              class="input-field"
            ></textarea>
          </div>
          <button type="submit" class="submit-button">Envoyer</button>
        </form>
        <button class="close-button" @click="toggleModal">Fermer</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      showModal: false,
      name: '',
      email: '',
      message: '',
      statusMessage: ''
    };
  },
  methods: {
    toggleModal() {
      this.showModal = !this.showModal;
    },
    async sendSupportMessage() {
      try {
        await axios.post('https://localhost:8000/api/contact', {
          name: this.name,
          email: this.email,
          message: this.message
        });
        this.statusMessage = 'Votre message a bien été envoyé !';
        this.toggleModal();
      } catch (error) {
        this.statusMessage = 'Erreur lors de l\'envoi du message.';
      }
    }
  }
};
</script>

<style scoped>
.support-icon {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #10B981;
  color: white;
  font-size: 24px;
  font-weight: bold;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.3);
  transition: background-color 0.3s;
}

.support-icon:hover {
  background-color: #059669;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
  position: relative;
}

.modal-title {
  font-size: 24px;
  color: #10B981;
  margin-bottom: 20px;
}

.input-field {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-top: 5px;
  margin-bottom: 10px;
  background-color: white; /* Fond blanc */
  color: black; /* Texte noir */
  transition: border-color 0.3s;
}

.input-field:focus {
  border-color: #10B981;
  outline: none;
}

.submit-button {
  background-color: #10B981;
  color: white;
  font-weight: bold;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  width: 100%;
  transition: background-color 0.3s;
}

.submit-button:hover {
  background-color: #059669;
}

.close-button {
  background: none;
  border: none;
  font-size: 16px;
  color: #666;
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
}

label {
    color: black;
}
</style>
