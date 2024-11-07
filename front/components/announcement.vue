<template>
    <section class="home">
        <div class="title">
            <img src="/medias/icons/annonce.svg" alt="megaphone">
            <h2>Les annonces</h2>
        </div>
        <p>Compagnons d’aventure recherchés ! Rejoins ou crée une équipe pour affronter ensemble les défis de Baldur’s
            Gate ou les mystères de Star Citizen.<br />
            <NuxtLink to="/announcement">Pour voir toutes les annonces, c'est par ici</NuxtLink>
        </p>

        <div class="annonces" v-if="latestAnnouncement && latestAnnouncement.length > 0">
            <div class="annonce" v-for="(annonce, index) in latestAnnouncement.slice(1)" :key="index">
                <NuxtLink :to="'/announcement/' + annonce.id">
                    <div class="content-annonce">
                        <div class="title-pastille">
                            <h3 class="line-clamp-2">{{ annonce.title }}</h3>
                            <Pastille>{{ annonce.game }}</Pastille>
                        </div>
                        <p class="line-clamp-3">{{ annonce.content }}</p>
                    </div>
                </NuxtLink>
            </div>
        </div>
    </section>
</template>

<script setup>
import { useAsyncData } from '#app';
import Pastille from '@/components/ui/pastille.vue';

const backUrl = 'https://localhost:8000';

// Utilisation de useAsyncData pour récupérer les dernières annonces
const { data: latestAnnouncement, pending, error } = await useAsyncData('latest-announcements', () => {
    return $fetch(`${backUrl}/api/announcement/latest`);
});

</script>

<style scoped>
.title {
    display: flex;
    align-items: end;

    img {
        margin-bottom: 16px;
        width: 50px;
        height: 50px;
        margin-right: 10px;
    }
}

p {
    margin-bottom: 20px;
}

.annonces {
    display: flex;
    gap: 20px;

    .annonce {
        flex: 1;
    }

    .content-annonce {
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        box-shadow: 2px 2px 5px #ccc;

        .pastille {
            margin-left: 20px;
        }

        .title-pastille {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;

            h3 {
                font-weight: bold;
            }
        }
    }
}
</style>