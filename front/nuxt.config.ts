// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  modules: ['@nuxt/ui'],
  css: [
    '@/assets/css/global.css'  // Chemin vers le fichier CSS global
  ],
  plugins: [
    '~/plugins/pinia.js'  // Assurez-vous que ce chemin est correct
  ]

})