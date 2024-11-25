<template>
   <a :href="link" class="hover:underline block ">{{ page }}</a>
</template>
  
<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  page: { type: String, default: 'Lien' },
  link: { type: String, default: '/' }
});
</script>
  
  <style scoped>
  button {
    cursor: pointer;
  }
  button:disabled {
    cursor: not-allowed;
    opacity: 0.6;
  }
  </style>
