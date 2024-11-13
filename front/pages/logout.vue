<template>
  <div class="logout-container">
    <h1>Déconnexion</h1>
    <p>Vous avez été déconnecté.</p>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';

const router = useRouter();

const logout = async () => {
  // Supprimez le token de localStorage pour déconnecter l'utilisateur
  localStorage.removeItem('token');

  // Appelez le backend pour informer de la déconnexion (facultatif)
  try {
    const response = await fetch('/api/logout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    if (!response.ok) {
      console.error('Erreur lors de la déconnexion du serveur');
    }
  } catch (error) {
    console.error('Erreur réseau lors de la déconnexion', error);
  }

  // Redirigez vers la page d'accueil ou la page de connexion
  router.push('/login');
};

// Appelez la fonction de déconnexion automatiquement lorsque le composant est monté
onMounted(() => {
  logout();
});
</script>

<style scoped>
.logout-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 100px;
}

h1 {
  font-size: 2rem;
  color: #333;
}

p {
  font-size: 1.2rem;
  color: #666;
}
</style>
