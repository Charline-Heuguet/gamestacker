<template>
    <div>
        <h1>Connectez-vous</h1>
        <form @submit.prevent="login">
            <div>
                <label for="email">Email:</label>
                <input type="email" v-model="email" id="email" required />
            </div>
            <div>
                <label for="password">Mot de passe:</label>
                <input type="password" v-model="password" id="password" required />
            </div>
            <button type="submit">Se connecter</button>
            <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth'; // Vérifie le chemin d'importation

const authStore = useAuthStore();
const email = ref('');
const password = ref('');
const errorMessage = ref('');

const login = async () => {
    errorMessage.value = '';
    try {
        await authStore.login(email.value, password.value);
        // Redirige ou effectue une action après une connexion réussie
        this.$router.push('/forum'); // Ajuste le chemin selon ta structure
    } catch (error) {
        errorMessage.value = 'Email ou mot de passe incorrect';
    }
};
</script>

<style>
.login-container {
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
}

.login-form {
    display: flex;
    flex-direction: column;
}

.form-group {
    margin-bottom: 15px;
}

label {
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

input:focus {
    border-color: #007bff;
    outline: none;
}

.btn {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #0056b3;
}

.error {
    color: red;
    text-align: center;
    margin-top: 10px;
}
</style>
