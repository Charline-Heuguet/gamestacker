<template>
  <div class="gutter">
    <header class="title">
      <!-- H1 enrichi avec des mots-clés pour le SEO -->
      <h1
        class="text-gray-900 dark:text-white text-3xl font-bold text-center my-8"
      >
        Game Stackers - Actualités, Annonces et Forums sur les Jeux Vidéo
      </h1>
    </header>

    <main>
      <!-- Section des derniers articles -->
      <section aria-label="Derniers Articles sur les Jeux Vidéo" class="mb-12">
        <div>
          <latestArticles />
        </div>
      </section>

      <!-- Section des annonces -->
      <section aria-label="Annonces et Parties de Jeux Vidéo" class="mb-12">
        <div>
          <announcement />
        </div>
      </section>

      <!-- Section des forums -->
      <section aria-label="Dernières Discussions du Forum" class="mb-12">
        <div>
          <LatestForum />
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import latestArticles from "~/components/latestArticles.vue";
import announcement from "~/components/announcement.vue";
import LatestForum from "~/components/latestForum.vue";
</script>

<style scoped>
.title {
  text-align: center;
  margin: 20px 0;
}

h2 {
  margin: 20px 0;
}
</style>
