// nuxt.config.ts
export default defineNuxtConfig({
  ssr: false,
  nitro: {
    devProxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        prependPath: true,
      }
    }
  },
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  modules: [
    '@nuxt/ui',
    'nuxt-swiper'],
  css: [
    '@/assets/css/global.css'
  ],
  plugins: [
    '~/plugins/pinia.js',
    '~/plugins/toast.client.js',
  ],
  app: {
    head: {
      script: [
        {
          src: 'https://www.google.com/recaptcha/api.js',
          async: true,
          defer: true
        }
      ],
      link: [
        {
          rel: 'preconnect',
          href: 'https://fonts.googleapis.com'
        },
        {
          rel: 'preconnect',
          href: 'https://fonts.gstatic.com',
          crossorigin: 'anonymous'
        },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'
        }
      ]
    }
  }
});
