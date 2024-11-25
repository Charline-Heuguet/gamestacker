<template>
  <section class="home">
    <div class="flex items-end mb-6">
      <img
        src="/medias/icons/annonce.svg"
        alt="megaphone"
        class="w-12 h-12 mr-3"
      />
      <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
        Les annonces
      </h2>
    </div>
    <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
      Compagnons d’aventure recherchés ! Rejoins ou crée une équipe pour
      affronter ensemble les défis de Baldur’s Gate ou les mystères de Star
      Citizen.<br />
      <NuxtLink
        to="/announcement"
        class="text-emerald-500 hover:underline dark:text-emerald-400"
      >
        Pour voir toutes les annonces, c'est par ici
      </NuxtLink>
    </p>

    <!-- Annonces Grid -->
    <div
      class="grid grid-cols-1 sm:grid-cols-2 gap-6"
      v-if="latestAnnouncement && latestAnnouncement.length > 0"
    >
      <div
        class="flex"
        v-for="(annonce, index) in latestAnnouncement.slice(1)"
        :key="index"
      >
        <NuxtLink :to="'/announcement/' + annonce.id" class="flex-1">
          <div
            class="flex flex-col h-full  p-4 rounded-md shadow-md bg-white dark:bg-neutral-800"
          >
            <div
              class="flex justify-between items-center mb-3"
            >
              <h3
                class="line-clamp-2 w-1/2 font-bold text-gray-800 dark:text-gray-100"
              >
                {{ annonce.title }}
              </h3>
              <Pastille class="ml-5 dark:bg-emerald-600 dark:text-gray-100">
                {{ annonce.game }}
              </Pastille>
            </div>
            <p class="line-clamp-3 text-gray-600 dark:text-gray-300">
              {{ annonce.content }}
            </p>
          </div>
        </NuxtLink>
      </div>
    </div>

    <!-- Message si aucune annonce -->
    <div
      v-else
      class="text-center py-8 text-gray-500 dark:text-gray-400"
    >
      Aucune annonce récente pour le moment.
    </div>
  </section>
</template>

  

<script setup>
import { useAsyncData } from '#app';
import Pastille from '@/components/ui/pastille.vue';

const backUrl = 'https://localhost:8000';

// Utilisation de useAsyncData pour récupérer les dernières annonces
const { data: latestAnnouncement, pending, error } = await useAsyncData('latest-announcements', () => {
    return $fetch(`${backUrl}/api/announcement/latest`);
});

</script>
