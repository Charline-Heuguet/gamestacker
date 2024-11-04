import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    token:
      typeof window !== "undefined"
        ? localStorage.getItem("authToken") || null
        : null,
    user: typeof window !== "undefined" ? parseUserFromLocalStorage() : null, // Utilisation d'une fonction pour parser en toute sécurité
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

        // Stocke le token et les informations de l'utilisateur
        this.token = response.token;
        this.user = response.user;

        // Sauvegarde dans localStorage uniquement si disponible
        if (typeof window !== "undefined") {
          localStorage.setItem("authToken", response.token);
          localStorage.setItem("user", JSON.stringify(response.user));
        }
      } catch (error) {
        throw error;
      }
    },
    logout() {
      // Réinitialise les propriétés Pinia
      this.token = null;
      this.user = null;

      // Supprime du localStorage uniquement si disponible
      if (typeof window !== "undefined") {
        localStorage.removeItem("authToken");
        localStorage.removeItem("user");
      }
    },
  },
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
});

// Fonction pour parser en toute sécurité l'utilisateur à partir du localStorage
function parseUserFromLocalStorage() {
  try {
    const user = localStorage.getItem("user");
    return user ? JSON.parse(user) : null;
  } catch (error) {
    console.error(
      "Erreur lors de l'analyse de l'utilisateur stocké dans le localStorage :",
      error
    );
    return null; // Renvoie null si une erreur est détectée
  }
}
