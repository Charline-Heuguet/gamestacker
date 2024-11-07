<template>
    <div class="gutter">
        <div class="title">
            <h1 class="text-gray-900">Game Stackers</h1>
            <p class="text-gray-900"> Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
        </div>

        <section>
            <latestArticles />
            <!-- <categories /> -->
             <announcement/>
             <LatestForum/>
        </section>
    </div>
</template>

<script setup>
import latestArticles from '~/components/latestArticles.vue';
import announcement from '~/components/announcement.vue';
import LatestForum from '~/components/latestForum.vue';
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
