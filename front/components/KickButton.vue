<template>
    <button
      @click="confirmKick"
      class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded"
    >
      Expulser
    </button>
  </template>
  
  <script setup>
  const props = defineProps({
    participant: {
      type: Object,
      required: true
    }
  })
  
  const emit = defineEmits(['confirm-kick'])
  
  const confirmKick = () => {
    emit('confirm-kick', props.participant)
  }
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
  