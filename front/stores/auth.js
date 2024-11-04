import { defineStore } from "pinia";
import { useStorage } from "@vueuse/core";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    token: useStorage("token", null),
  }),
  actions: {
    async register(email, password) {
      try {
        await $fetch("/api/register", {
          method: "POST",
          body: { email, password },
        });
        console.log("User registered successfully");
      } catch (error) {
        console.error("Erreur lors de l'inscription:", error);
        throw error;
      }
    },
    async login(email, password) {
      try {
        const response = await $fetch("https://localhost:8000/api/login", {
          method: "POST",
          body: { email, password },
        });
        this.token = response.token;
      } catch (error) {
        console.error("Erreur de login:", error);
        throw error;
      }
    },
    logout() {
      this.token = null;
    },
  },
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
});
