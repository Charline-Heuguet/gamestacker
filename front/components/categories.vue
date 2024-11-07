<template>
    <div class="categories">
        <button v-for="category in categories" :key="category.id">
            {{ category.name }}
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
const categories = ref([]);

const backUrl = 'https://localhost:8000';

onMounted(async () => {
    try {
        const response = await fetch(backUrl + '/api/category');
        if (!response.ok) {
            throw new Error('Erreur lors de la récupération des catégories');
        }
        categories.value = await response.json();
    } catch (error) {
        console.error('Erreur lors de la récupération des catégories:', error);
    }
});
</script>

<style scoped>
.categories{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin: 20px 0;

    button{
        padding: 10px 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        background-color: #f1f1f1;
        cursor: pointer;
        color: gray;
    }
}
</style>