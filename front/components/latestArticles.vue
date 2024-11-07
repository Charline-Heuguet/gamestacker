<template>
    <section class="home articles">
        <div class="latest-articles">
            <div class="title">
                <img src="/medias/icons/news.svg" alt="megaphone">
                <h2>Nos derniers articles</h2>
            </div>
            <UButton to="/articles" color="gray" variant="solid" class="mb-4 mt-14">
                Voir nos articles
            </UButton>
        </div>
        <!-- Premier article -->
        <div class="first-article" v-if="article.length > 0">
            <img :src="article[0].image ? article[0].image : defaultImage" alt="Image de l'article">
            <h3>{{ article[0].title }}</h3>
            <p>{{ article[0].content }}</p>
        </div>
        <!-- Swiper Slider -->
        <div class="swiper-container">
            <swiper :slides-per-view="3.5" space-between="20" :loop="true" class="mySwiper">
                <swiper-slide v-for="(art, index) in article.slice(1)" :key="index" class="article slide">
                    <NuxtLink :to="'/article/' + art.id">
                        <div class="content-article">
                            <img :src="art.image ? art.image : defaultImage" alt="Image de l'article">
                            <h3>{{ art.title }}</h3>
                            <p class="line-clamp-3">{{ art.content }}</p>
                        </div>
                    </NuxtLink>
                </swiper-slide>
            </swiper>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/swiper-bundle.css';
import defaultImage from '/medias/img-par-defaut.webp';

const backUrl = 'https://localhost:8000';

const article = ref([]);

onMounted(async () => {
    try {
        const response = await fetch(backUrl + '/api/article/last-articles');
        if (!response.ok) {
            throw new Error('Erreur lors de la récupération des derniers articles');
        }
        article.value = await response.json();
    } catch (error) {
        console.error('Erreur lors de la récupération des derniers articles:', error);
    }
});
</script>

<style scoped>
.articles {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 25px;

    .first-article {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 10px 5px 5px gray;
        background-color: rgba(255, 255, 255, 0.931);

        img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    }

    .latest-articles {
        display: flex;
        justify-content: space-between;
        align-items: center;

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
    }

    .article {
        border: 1px solid #ccc;
        border-radius: 10px;
        overflow: hidden;
        background-color: rgba(255, 255, 255, 0.931);
        height: 300px;

        img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    }

    h3 {
        font-weight: bold;
        margin: 0;
    }

    h3,
    p {
        padding: 10px;
        color: #584f4f;
    }


    .swiper-container {
        width: 100%;
        max-width: 1200px;
        max-height: 350px;
        margin: 0 auto;
        position: relative;

        .content-article {

            img {
                width: 100%;
                height: 150px;
                object-fit: cover;
            }
        }
    }

    .swiper-slide {
        display: flex;
        justify-content: center;
        align-items: center;

        .slide {
            width: 150px;
            border-radius: 10px;
            box-shadow: 10px 5px 5px gray;
            /* Ombre douce */
            transition: box-shadow 0.3s ease;
            /* Transition douce pour l'ombre */
        }
    }
}
</style>