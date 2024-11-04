<template>
    <div class="signup-container min-h-screen flex items-center justify-center bg-white px-4 py-12">
        <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-neumorphism">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Inscription</h1>
            <form @submit.prevent="handleSignup" class="space-y-6">
                <div class="form-group">
                    <label for="email" class="block text-sm font-medium text-gray-600">Email :</label>
                    <input type="email" id="email" v-model="form.email" required class="input-field" />
                </div>
                
                <div class="form-group">
                    <label for="pseudo" class="block text-sm font-medium text-gray-600">Pseudo :</label>
                    <input type="text" id="pseudo" v-model="form.pseudo" required class="input-field" />
                </div>
                
                <div class="form-group">
                    <label for="age" class="block text-sm font-medium text-gray-600">Âge :</label>
                    <input type="number" id="age" v-model.number="form.age" required class="input-field" />
                </div>
                
                <div class="form-group">
                    <label for="password" class="block text-sm font-medium text-gray-600">Mot de passe :</label>
                    <input type="password" id="password" v-model="form.password" required class="input-field" />
                </div>
                
                <div class="form-group">
                    <label for="description" class="block text-sm font-medium text-gray-600">Description :</label>
                    <textarea id="description" v-model="form.description" class="input-field"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="discord" class="block text-sm font-medium text-gray-600">Discord :</label>
                    <input type="text" id="discord" v-model="form.discord" class="input-field" />
                </div>
                
                <div class="form-group">
                    <label for="gender" class="block text-sm font-medium text-gray-600">Genre :</label>
                    <select id="gender" v-model="form.gender" class="input-field">
                        <option value="male">Homme</option>
                        <option value="female">Femme</option>
                        <option value="non-binary">Non-binaire</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">S'inscrire</button>
            </form>

            <p v-if="errorMessage" class="error-message text-center mt-4">{{ errorMessage }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const form = ref({
    email: '',
    pseudo: '',
    age: null,
    password: '',
    description: '',
    discord: '',
    gender: 'other',
});

const errorMessage = ref(null);
const router = useRouter();

const handleSignup = async () => {
    try {
        const response = await $fetch('https://localhost:8000/api/signup', {
            method: 'POST',
            body: form.value,
        });

        if (response.error) {
            errorMessage.value = response.error;
        } else {
            router.push('/login');
        }
    } catch (error) {
        errorMessage.value = "Une erreur s'est produite lors de l'inscription.";
    }
};
</script>

<style scoped>
/* Style général du conteneur d'inscription avec effet de neumorphisme */
.signup-container {
    background-color: #f9f9f9;
}

/* Effet de Neumorphisme */
.shadow-neumorphism {
    box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
}

/* Style du titre */
h1 {
    font-size: 2.5rem;
    color: #333;
}

/* Style des groupes de champs et labels */
.form-group {
    margin-bottom: 15px;
}

label {
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

/* Champs de saisie avec touches de vert */
.input-field {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #f5f5f5;
    color: #333;
    font-size: 14px;
    transition: border-color 0.3s;
}

.input-field:focus {
    border-color: #34d399; /* Vert émeraude */
    outline: none;
}

/* Bouton avec couleur verte */
.btn {
    width: 100%;
    padding: 12px;
    background-color: #34d399; /* Vert émeraude */
    color: white;
    font-weight: bold;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #059669; /* Vert émeraude plus foncé au survol */
}

/* Message d'erreur */
.error-message {
    color: #f87171; /* Rouge pour le message d'erreur */
    text-align: center;
    margin-top: 10px;
}
</style>
