<template>
  <div class="add-comment p-4 bg-gray-100 dark:bg-neutral-700 rounded-lg">
    <h3 class="text-xl font-semibold text-emerald-500 mb-4">Prenez part à la discussion</h3>
    <div class="relative">
      <textarea
        v-model="content"
        :disabled="!isAuthenticated"
        :class="['w-full p-3 rounded-lg mb-4 resize-none focus:ring focus:ring-emerald-500', isAuthenticated ? 'bg-white text-black' : 'bg-neutral-300 text-gray-500']"
        rows="3"
        placeholder="Écrivez votre commentaire ici..."
      ></textarea>
      <div v-if="!isAuthenticated" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-300 bg-opacity-75 rounded-lg">
        <p class="text-gray-500 mb-2">Veuillez vous connecter pour écrire un commentaire</p>
        <NuxtLink
          to="/login"
          class="px-4 py-2 text-sm font-medium text-white bg-emerald-500 rounded-lg hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300"
        >
          Se connecter
        </NuxtLink>
      </div>
    </div>
    <small><p class="text-neutral-400 mb-5">N'oubliez pas que votre commentaire est soumis à la modération</p></small>
    <button
      @click="submitComment"
      :disabled="!isAuthenticated"
      class="bg-emerald-500 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
    >
      Envoyer
    </button>
    <div v-if="errorMessage" class="text-red-500 mt-2">{{ errorMessage }}</div>
  </div>
</template>

<script setup>
import { ref, defineEmits, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const backendUrl = 'http://localhost:8000';
const content = ref('');
const errorMessage = ref('');
const route = useRoute();
const emit = defineEmits(['commentAdded']);

// Récupérer la fonction $toast depuis le contexte de l'application
const { $toast } = useNuxtApp();

const authStore = useAuthStore();
const isAuthenticated = computed(() => authStore.isAuthenticated);

const props = defineProps({
  targetType: {
    type: String,
    required: true,
    validator: (value) => ['article', 'forum'].includes(value),
  },
  targetId: {
    type: Number,
    required: true,
  },
});

const apiUrl = computed(() => {
  return `${backendUrl}/api/comment/${props.targetId}/add-forum`;
});

const submitComment = async () => {
  
  $toast.success("Commentaire ajouté avec succès !");

    errorMessage.value = '';
    if (!content.value.trim()) {
        errorMessage.value = 'Le contenu du commentaire ne peut pas être vide.';
        return;
    }

    console.log('URL API :', apiUrl.value);
    console.log('Contenu du commentaire :', content.value);

    try {
        const response = await fetch(apiUrl.value, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${authStore.token}`
            },
            body: JSON.stringify({ content: content.value }),
        });

        if (!response.ok) {
            const data = await response.json();
            throw new Error(data.status || "Erreur lors de l'ajout du commentaire");
        }

        const newCommentId = await response.json().id;

        content.value = '';
        errorMessage.value = '';
        emit('commentAdded', newCommentId);
    } catch (error) {
        console.error("Erreur lors de l'ajout du commentaire:", error);
        errorMessage.value = error.message;
        $toast.error("Une erreur s'est produite !")
    }
};


</script>

<style scoped>
.add-comment {
  max-width: 1000px;
  margin: 0 auto;
}


.bg-gray-300 {
  background-color: #e5e7eb;
}
</style>
