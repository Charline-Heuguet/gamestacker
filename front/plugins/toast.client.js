// plugins/toast.client.js
import { createToastInterface } from "vue-toastification";
import "vue-toastification/dist/index.css";

export default defineNuxtPlugin((nuxtApp) => {
    const toast = createToastInterface({
        position: "bottom-right",
        timeout: 8000,
        closeOnClick: true,
        pauseOnHover: true,
    });

    // Injecter `toast` dans le contexte de Nuxt
    nuxtApp.provide("toast", toast);
});
