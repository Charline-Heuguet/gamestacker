<template>
    <div class="announcement-container p-8">
      <div v-if="loading" class="text-center text-gray-500">Chargement de l'annonce...</div>
      <div v-else-if="announcement">
        <h1 class="text-2xl font-bold mb-4">{{ announcement.title }}</h1>
  
        <div class="flex flex-col lg:flex-row">
          <div class="announcement-details bg-gray-800 text-white p-6 rounded-lg shadow-lg flex-1">
            <div class="user-info flex items-center mb-4">
              <img
                :src="announcement.user.image ? `${backendUrl}/images/users/${announcement.user.image}` : `${backendUrl}/images/users/default.jpg`"
                alt="Image de l'utilisateur"
                class="w-16 h-16 rounded-full object-cover mr-4"
              />
              <div>
                <h2 class="text-lg font-semibold text-emerald-500">{{ announcement.user.pseudo }}</h2>
                <p class="text-gray-400">Discord : <span class="text-emerald-800 hover:text-emerald-600">{{ announcement.user.discord }}</span></p>
              </div>
            </div>
  
            <p><strong>Jeu :</strong> {{ announcement.game }}</p>
            <p><strong>Date :</strong> {{ formatDate(announcement.date) }}</p>
            <p><strong>Nombre maximum de joueurs :</strong> {{ announcement.max_nb_players }}</p>
            <p><strong>Room ID :</strong> <span class="text-emerald-800 hover:text-emerald-600">{{ announcement.roomId }}</span></p>
            <p class="mt-4">{{ announcement.content }}</p>
          </div>
  
          <div class="participants-list bg-gray-900 text-white p-6 rounded-lg shadow-lg mt-6 lg:mt-0 lg:ml-6 lg:w-1/3">
            <h3 class="text-xl font-semibold mb-4">Participants</h3>
            <div v-for="(participant, index) in announcement.participants" :key="participant.id" class="participant-info flex items-center mb-4">
              <img
                :src="participant.image ? `${backendUrl}/images/users/${participant.image}` : `${backendUrl}/images/users/default.jpg`"
                alt="Image du participant"
                class="w-12 h-12 rounded-full object-cover mr-4"
              />
              <div class="flex-1">
                <h4 class="font-semibold text-emerald-500">{{ participant.pseudo }}</h4>
                <p class="text-gray-400">Discord : <span class="text-emerald-800 hover:text-emerald-600">{{ participant.discord }}</span></p>
              </div>
              <!-- Bouton d'expulsion -->
              <KickButton
                :participant="participant"
                @confirm-kick="confirmKick"
              />
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-center text-red-500">Annonce non trouvée.</div>
  
      <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="modal bg-gray-800 text-white p-8 rounded-lg shadow-lg max-w-md w-full">
          <h3 class="text-xl font-semibold mb-4">Confirmer l'expulsion</h3>
          <p class="mb-6">Êtes-vous sûr de vouloir expulser le joueur "{{ selectedParticipant.pseudo }}" ?</p>
          <div class="flex justify-end space-x-4">
            <button @click="kickConfirmed" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
              Oui, expulser
            </button>
            <button @click="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
              Annuler
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { useRoute } from 'vue-router'
  import KickButton from '@/components/KickButton.vue'
  
  const backendUrl = 'http://localhost:8000'
  const announcement = ref(null)
  const loading = ref(true)
  const route = useRoute()
  const id = route.params.id
  
  const showModal = ref(false)
  const selectedParticipant = ref(null)
  
  const confirmKick = (participant) => {
    selectedParticipant.value = participant
    showModal.value = true
  }
  
  const closeModal = () => {
    showModal.value = false
    selectedParticipant.value = null
  }
  
  const kickConfirmed = async () => {
    try {
      const response = await fetch(`${backendUrl}/api/announcement/${id}/kick/${selectedParticipant.value.id}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
      })
      
      const data = await response.json()
  
      if (!response.ok) {
        alert(`Erreur : ${data.status}`)
      } else {
        alert(`Succès : ${data.status}`)
        removeParticipant(selectedParticipant.value.id)
      }
    } catch (error) {
      console.error('Erreur lors de l’expulsion du participant:', error)
    } finally {
      closeModal()
    }
  }
  
  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    })
  }
  
  const fetchAnnouncement = async () => {
    try {
      const response = await fetch(`${backendUrl}/api/announcement/${id}`)
      if (!response.ok) {
        throw new Error(`Erreur HTTP : ${response.statusText}`)
      }
      announcement.value = await response.json()
    } catch (error) {
      console.error('Erreur lors de la récupération de l’annonce:', error)
    } finally {
      loading.value = false
    }
  }
  
  const removeParticipant = (participantId) => {
    announcement.value.participants = announcement.value.participants.filter(
      participant => participant.id !== participantId
    )
  }
  
  onMounted(() => {
    fetchAnnouncement()
  })
  </script>
  
  <style scoped>
  .announcement-container {
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .announcement-details, .participants-list {
    background-color: #222;
    border: 1px solid #444;
    border-radius: 8px;
  }
  
  .participant-info img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
  }
  </style>
  