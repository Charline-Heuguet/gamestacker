<template>
    <li>
        <a :href="link" class="block py-2 px-3 text-black rounded md:bg-transparent md:p-0 header-link dark:text-white">{{ page }}</a> 
    </li>
</template>
  
<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  page: { type: String, default: 'Page' },
  link: { type: String, default: '/' }
});
</script>
  
  <style scoped>
.header-link:hover{
  color: rgb(52, 211, 153);
}
  </style>
