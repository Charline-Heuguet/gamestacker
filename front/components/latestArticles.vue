<template>
  <section class="my-12 articles">
    <div class="latest-articles flex justify-between items-center mb-6">
      <div class="title flex items-center space-x-3">
        <img src="/medias/icons/news.svg" alt="megaphone" class="w-10 h-10" />
        <h2 class="text-2xl font-bold text-gray-800 dark:text-emerald-500">
          Nos derniers articles
        </h2>
      </div>
      <UButton to="/article" color="emerald" variant="solid">
        Voir nos articles
      </UButton>
    </div>

    <!-- Premier article -->
    <div
      class="first-article mb-8 dark:border dark:border-neutral-900 dark:bg-neutral-800 dark:text-gray-100"
      v-if="article.length > 0"
    >
      <img
        :src="
          article[0].image
            ? `${backendUrl}/images/articles/${article[0].image}`
            : defaultImage
        "
        alt="Image de l'article"
        class="w-full h-48 object-cover rounded-t-lg"
      />
      <div class="p-4 bg-white shadow-md rounded-b-lg dark:bg-neutral-800">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">
          {{ article[0].title }}
        </h3>
        <p class="text-gray-600 dark:text-gray-300 line-clamp-3 mb-4">
          {{ stripHTML(article[0].content) }}
        </p>

        <NuxtLink
          :to="'/article/' + article[0].id"
          class="text-emerald-500 hover:underline dark:text-emerald-400"
        >
          Lire l'article
        </NuxtLink>
      </div>
    </div>

    <!-- Swiper Slider -->
    <div class="swiper-container">
      <swiper
        :slides-per-view="getSlidesPerView"
        space-between="20"
        :loop="true"
        :modules="[Autoplay]"
        :autoplay="{ delay: 2000, disableOnInteraction: false }"
        class="mySwiper"
      >
        <swiper-slide
          v-for="(art, index) in article.slice(1)"
          :key="index"
          class="article-card bg-slate-50 rounded-lg shadow-xxl overflow-hidden dark:border dark:border-neutral-900 transition-transform transform hover:scale-105 hover:shadow-lg dark:bg-neutral-800 dark:bg-neutral-800"
        >
          <NuxtLink :to="'/article/' + art.id" class="block h-full">
            <div class="content-article flex flex-col h-full">
              <img
                :src="
                  art.image
                    ? `${backendUrl}/images/articles/${art.image}`
                    : defaultImage
                "
                alt="Image de l'article"
                class="w-full h-40 object-cover rounded-t-lg"
              />
              <div class="p-4 flex-1 flex flex-col dark:bg-neutral-800">
                <h3
                  class="text-lg font-semibold line-clamp-2 text-gray-800 dark:text-gray-100 mb-2"
                >
                  {{ art.title }}
                </h3>
                <p
                  class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 mb-4"
                >
                  {{ art.content }}
                </p>
                <NuxtLink
                  :to="'/article/' + art.id"
                  class="mt-auto text-sm font-medium text-emerald-500 hover:underline dark:text-emerald-400"
                >
                  Voir l'article
                </NuxtLink>
              </div>
            </div>
          </NuxtLink>
        </swiper-slide>
      </swiper>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay } from "swiper/modules";
import "swiper/swiper-bundle.css";
import defaultImage from "/medias/img-par-defaut.webp";

const backendUrl = "https://localhost:8000";
const article = ref([]);

const getSlidesPerView = ref(1);

onMounted(async () => {
  try {
    const response = await fetch(`${backendUrl}/api/article/last-articles`);
    if (!response.ok) {
      throw new Error("Erreur lors de la récupération des derniers articles");
    }
    article.value = await response.json();
  } catch (error) {
    console.error(
      "Erreur lors de la récupération des derniers articles:",
      error
    );
  }

  updateSlidesPerView(); // Initialise la valeur au chargement
  window.addEventListener("resize", updateSlidesPerView);
});

function updateSlidesPerView() {
  const width = window.innerWidth;
  if (width >= 1024) {
    getSlidesPerView.value = 4;
  } else if (width >= 768) {
    getSlidesPerView.value = 2;
  } else {
    getSlidesPerView.value = 1;
  }
}

// Fonction pour retirer les balises HTML
function stripHTML(html) {
  return html.replace(/<[^>]*>/g, ""); // Supprime toutes les balises HTML
}
</script>

<style scoped>
.articles {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 25px;
}

.first-article {
  width: 100%;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.swiper-container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

.article-card {
  display: flex;
  flex-direction: column;
  margin-bottom: 15px;
}

.content-article {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
