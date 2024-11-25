<template>
  <section class="h-screen w-screen flex items-center justify-center bg-[#dcdedb] relative">
    <button @click="handleError" class="text-white bg-[#08946c] p-2 rounded-lg absolute top-4 left-4">Revenir à l'accueil</button>
    <div class="bg-[url('/medias/page404.webp')] bg-contain bg-no-repeat bg-center h-full w-full">
    </div>
  </section>
</template>

<script setup lang="ts">
import type { NuxtError } from '#app'

const props = defineProps({
  error: Object as () => NuxtError
})

const handleError = () => clearError({ redirect: '/' })
</script>
