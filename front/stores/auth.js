import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    token:
      typeof window !== "undefined"
        ? localStorage.getItem("authToken") || null
        : null,
    user: typeof window !== "undefined" ? parseUserFromLocalStorage() : null,
  }),
  actions: {
    async register(email, password) {
      try {
        await this.apiClient("/api/register", {
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
        const response = await this.apiClient(
          "https://localhost:8000/api/login",
          {
            method: "POST",
            body: { email, password },
          }
        );

        this.token = response.token;
        this.user = response.user;

        if (typeof window !== "undefined") {
          localStorage.setItem("authToken", response.token);
          localStorage.setItem("user", JSON.stringify(response.user));
        }
      } catch (error) {
        throw error;
      }
    },
    logout() {
      this.token = null;
      this.user = null;

      if (typeof window !== "undefined") {
        localStorage.removeItem("authToken");
        localStorage.removeItem("user");
      }
    },
    async apiClient(url, options = {}) {
      const headers = options.headers || {};

      // Ajoute le token JWT si l'utilisateur est authentifié
      if (this.token) {
        headers["Authorization"] = `Bearer ${this.token}`;
      }

      // Fusionne les options de la requête avec les en-têtes modifiés
      return await $fetch(url, {
        ...options,
        headers,
      });
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
    return null;
  }
}
