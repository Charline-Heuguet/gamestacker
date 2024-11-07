<template>
    <div class=" add-comment p-4 bg-zinc-300 rounded-lg shadow-xl">
      <h3 class="text-xl font-semibold text-emerald-500 mb-4">Prenez part à la discution</h3>
      <textarea
        v-model="content"
        class="w-full p-3 rounded-lg bg-zinc-400 text-white mb-4 resize-none focus:ring focus:ring-emerald-500"
        rows="3"
        placeholder="Écrivez votre commentaire ici..."
      ></textarea>
      <small><p class="text-neutral-400">N'oubliez pas que votre commentaire est soumis à la modération</p></small>
      <button
        @click="submitComment"
        class="bg-emerald-500 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded"
      >
        Envoyer
      </button>
      <div v-if="errorMessage" class="text-red-500 mt-2">{{ errorMessage }}</div>
    </div>
  </template>
  
  <script setup>
  import { ref, defineEmits } from 'vue'
  import { useRoute } from 'vue-router'
  import { useAuthStore } from '@/stores/auth';

  
  const backendUrl = 'https://localhost:8000'
  const content = ref('')
  const errorMessage = ref('')
  const route = useRoute()
  const emit = defineEmits(['commentAdded'])

  const authStore = useAuthStore()
  const isAuthenticated = authStore.isAuthenticated
  
  // Fonction pour envoyer le commentaire
  const submitComment = async () => {
    errorMessage.value = ''
    if (!content.value.trim()) {
      errorMessage.value = 'Le contenu du commentaire ne peut pas être vide.'
      return
    }
  
    try {
      const response = await fetch(`${backendUrl}/api/comment/${route.params.id}/add-article`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${authStore.token}`
        },
        body: JSON.stringify({ content: content.value })
      })
  
      if (!response.ok) {
        const data = await response.json()
        throw new Error(data.status || 'Erreur lors de l\'ajout du commentaire')
      }

      const newCommentId = await response.json().id
  
      // Effacer le contenu et émettre un événement de succès pour rafraîchir les commentaires
      content.value = ''
      errorMessage.value = ''
      emit('commentAdded', newCommentId) // Émet un événement lorsque le commentaire est ajouté
    } catch (error) {
      console.error('Erreur lors de l\'ajout du commentaire:', error)
      errorMessage.value = error.message
    }
  }
  </script>
  
  <style scoped>
  .add-comment {
    max-width: 1000px;
    margin: 0 auto;
  }

  .bg-zinc-300 {
    background-color: #f9f9fb;
  }
  .bg-zinc-400 {
    background-color: rgb(212 212 216);
  }
  </style>
  