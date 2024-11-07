<template>
    <div class="announcement-container p-8">
        <div v-if="loading" class="text-center text-gray-500">Chargement de l'annonce...</div>
        <div v-else-if="announcement">
            <h1 class="text-2xl font-bold text-white mb-4">{{ announcement.title }}</h1>

            <div class="flex flex-col lg:flex-row">
                <div class="announcement-details bg-gray-800 text-white p-6 rounded-lg shadow-lg flex-1">
                    <!-- Informations de l'utilisateur -->
                    <div class="user-info flex items-center mb-4" v-if="announcement.user">
                        <img
                            :src="announcement.user.image ? `${backendUrl}/images/users/${announcement.user.image}` : `${backendUrl}/images/users/default.jpg`"
                            alt="Image de l'utilisateur"
                            class="w-16 h-16 rounded-full object-cover mr-4"
                        />
                        <div>
                            <h2 class="text-lg font-semibold text-emerald-500">{{ announcement.user.pseudo }}</h2>
                            <p class="text-gray-400">Discord : <span class="text-emerald-500 hover:text-emerald-300">{{ announcement.user.discord }}</span></p>
                        </div>
                    </div>

                    <p><strong class="text-gray-300">Jeu :</strong> {{ announcement.game }}</p>
                    <p><strong class="text-gray-300">Date :</strong> {{ formatDate(announcement.date) }}</p>
                    <p><strong class="text-gray-300">Nombre maximum de joueurs :</strong> {{ announcement.max_nb_players }}</p>
                    <p><strong class="text-gray-300">Room ID :</strong> <span class="text-emerald-500 hover:text-emerald-300">{{ announcement.roomId }}</span></p>
                    <p class="mt-4">{{ announcement.content }}</p>

                    <button 
                        @click="openJoinModal" 
                        :disabled="announcement.participants.length >= announcement.max_nb_players"
                        :class="{
                            'bg-gray-400 cursor-not-allowed': announcement.participants.length >= announcement.max_nb_players,
                            'bg-emerald-500 hover:bg-emerald-600': announcement.participants.length < announcement.max_nb_players
                        }"
                        class="mt-6 w-full text-white font-semibold py-2 px-4 rounded"
                    >
                        Rejoindre le salon
                    </button>
                </div>

                <div class="participants-list bg-gray-900 text-white p-6 rounded-lg shadow-lg mt-6 lg:mt-0 lg:ml-6 lg:w-1/3">
                    <h3 class="text-xl font-semibold mb-4 text-white">Participants</h3>
                    <div v-for="(participant, index) in announcement.participants" :key="participant.id" class="participant-info flex items-center mb-4">
                        <img
                            :src="participant.image ? `${backendUrl}/images/users/${participant.image}` : `${backendUrl}/images/users/default.jpg`"
                            alt="Image du participant"
                            class="w-12 h-12 rounded-full object-cover mr-4"
                        />
                        <div class="flex-1">
                            <h4 class="font-semibold text-emerald-500">{{ participant.pseudo }}</h4>
                            <p class="text-gray-400">Discord : <span class="text-emerald-500 hover:text-emerald-300">{{ participant.discord }}</span></p>
                        </div>
                        <!-- Bouton d'expulsion, visible seulement si l'utilisateur actuel est le créateur -->
                        <button 
                            v-if="isOwner" 
                            @click="confirmKick(participant)" 
                            class="bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded">
                            Expulser
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="text-center text-red-500">Annonce non trouvée.</div>

        <!-- Modal de confirmation pour rejoindre le salon -->
        <div v-if="showJoinModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="modal bg-gray-800 text-white p-8 rounded-lg shadow-lg max-w-4xl w-full">
                <h3 class="text-xl font-semibold mb-4">Confirmer l'action</h3>
                <p class="mb-6">Voulez-vous rejoindre le salon "{{ announcement.title }}" ?</p>
                <div class="flex justify-end space-x-4">
                    <button @click="confirmJoin" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded">
                        Oui, rejoindre
                    </button>
                    <button @click="closeJoinModal" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                        Annuler
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de confirmation pour expulsion -->
        <div v-if="showKickModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="modal bg-gray-800 text-white p-8 rounded-lg shadow-lg max-w-4xl w-full">
                <h3 class="text-xl font-semibold mb-4">Confirmer l'expulsion</h3>
                <p class="mb-6">Êtes-vous sûr de vouloir expulser le joueur "{{ selectedParticipant.pseudo }}" ?</p>
                <div class="flex justify-end space-x-4">
                    <button @click="kickConfirmed" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
                        Oui, expulser
                    </button>
                    <button @click="closeKickModal" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const backendUrl = 'http://localhost:8000';
const announcement = ref(null);
const loading = ref(true);
const route = useRoute();
const authStore = useAuthStore();
const id = route.params.id;

const showJoinModal = ref(false);
const showKickModal = ref(false);
const selectedParticipant = ref(null);

const userReady = ref(false); // Indique si les données utilisateur sont chargées

// Détermine si l'utilisateur actuel est le créateur de l'annonce
const isOwner = computed(() => {
  if (!userReady.value || !announcement.value?.user) {
    console.log('Données utilisateur ou annonce non disponibles');
    return false;
  }
  console.log('ID de l’utilisateur connecté:', authStore.user?.id);
  console.log('ID du créateur de l’annonce:', announcement.value?.user?.id);
  return authStore.user?.id === announcement.value?.user?.id;
});

// Observer authStore.user pour détecter quand il est chargé
watch(
  () => authStore.user,
  (newUser) => {
    if (newUser) {
      console.log("Utilisateur chargé :", newUser);
      userReady.value = true; // Indique que les données utilisateur sont prêtes
    }
  },
  { immediate: true }
);

const openJoinModal = () => {
  showJoinModal.value = true;
};

const closeJoinModal = () => {
  showJoinModal.value = false;
};

const confirmJoin = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/announcement/${id}/join`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authStore.token}`,
      },
    });
    console.log(authStore.token);
    const data = await response.json();

    if (!response.ok) {
      alert(`Erreur : ${data.status}`);
    } else {
      alert(`Succès : Vous avez rejoint le salon`);
      window.location.reload();
    }
  } catch (error) {
    console.error('Erreur lors de la tentative de rejoindre le salon :', error);
  } finally {
    closeJoinModal();
  }
};

const confirmKick = (participant) => {
  selectedParticipant.value = participant;
  showKickModal.value = true;
};

const closeKickModal = () => {
  showKickModal.value = false;
  selectedParticipant.value = null;
};

const kickConfirmed = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/announcement/${id}/kick/${selectedParticipant.value.id}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authStore.token}`,
      },
    });
    
    const data = await response.json();

    if (!response.ok) {
      alert(`Erreur : ${data.status}`);
    } else {
      alert(`Succès : Vous avez expulsé le participant`);
      removeParticipant(selectedParticipant.value.id);
    }
  } catch (error) {
    console.error('Erreur lors de l’expulsion du participant:', error);
  } finally {
    closeKickModal();
  }
};



const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const fetchAnnouncement = async () => {
  try {
    const response = await fetch(`${backendUrl}/api/announcement/${id}`);
    if (!response.ok) {
      throw new Error(`Erreur HTTP : ${response.statusText}`);
    }
    const data = await response.json();
    if (!data || !data.id) {
      throw new Error("Les données de l'annonce sont incorrectes");
    }
    announcement.value = data;

    // Vérification après récupération de l'annonce
    console.log('Annonce récupérée:', announcement.value);
  } catch (error) {
    console.error('Erreur lors de la récupération de l’annonce:', error);
  } finally {
    loading.value = false;
  }
};

const removeParticipant = (participantId) => {
  announcement.value.participants = announcement.value.participants.filter(
    participant => participant.id !== participantId
  );
};

onMounted(() => {
  fetchAnnouncement();
});
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

.text-emerald-500 {
    color: #34d399;
}

.modal h3 {
    color: #ffffff;
}

.modal p {
    color: #cbd5e0;
}
</style>
