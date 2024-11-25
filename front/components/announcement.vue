<template>
    <section class="home">
      <div class="flex items-end">
        <img
          src="/medias/icons/annonce.svg"
          alt="megaphone"
          class="mb-4 w-12 h-12 mr-2.5"
        />
        <h2 class="uppercase text-2xl font-bold text-gray-800">Les annonces</h2>
      </div>
      <p class="text-gray-600 mb-5">
        Compagnons d’aventure recherchés ! Rejoins ou crée une équipe pour
        affronter ensemble les défis de Baldur’s Gate ou les mystères de Star
        Citizen.<br />
        <NuxtLink to="/announcement"
          >Pour voir toutes les annonces, c'est par ici</NuxtLink
        >
      </p>
  
      <div
        class="grid grid-cols-2 gap-5"
        v-if="latestAnnouncement && latestAnnouncement.length > 0"
      >
        <div
          class="flex"
          v-for="(annonce, index) in latestAnnouncement.slice(1)"
          :key="index"
        >
          <NuxtLink :to="'/announcement/' + annonce.id" class="flex-1">
            <div
              class="flex flex-col h-full border border-gray-300 p-2.5 rounded-md shadow"
            >
              <div
                class="flex justify-between items-center mb-2.5"
              >
                <h3
                  class="line-clamp-2 w-1/2 font-bold"
                >
                  {{ annonce.title }}
                </h3>
                <Pastille class="ml-5">{{ annonce.game }}</Pastille>
              </div>
              <p class="line-clamp-3">{{ annonce.content }}</p>
            </div>
          </NuxtLink>
        </div>
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
