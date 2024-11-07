<template>
<header>
    <div class="bg-emerald-600 text-white text-center py-2 overflow-hidden">
      <div class="animate-marquee whitespace-nowrap">
          <span class="mx-4">
              🚀 Ne manquez rien ! Rejoignez des joueurs passionnés et découvrez nos dernières actus jeux vidéos.
              <a href="/announcement" class="underline font-semibold hover:text-emerald-200">Cliquez ici pour les annonces 🎮</a>
          </span>
      </div>
    </div>
    <nav class="bg-white border-b border-black">
      <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <!-- Logo et Nom du site -->
        <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
          <img src="@/assets/img/logo_GameStaker_black.webp" class="h-10" alt="GameStaker Logo" />
        </a>

        <!-- Bouton menu burger (visible en mode mobile) -->
        <button type="button" aria-controls="navbar-menu" aria-expanded="false"
          class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
          @click="toggleMenu">
          <span class="sr-only">Ouvrir le meny</span>
          <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M1 1h15M1 7h15M1 13h15" />
          </svg>
        </button>

        <!-- Menu de navigation (visible en mode desktop et mobile) -->
        
        <div :class="['w-full uppercase font-semibold md:flex md:w-auto', isMenuOpen ? '' : 'hidden']" id="navbar-menu">
          <ul class="flex flex-col p-4 md:p-0 mt-4 font-semibold border border-gray-100 rounded-lg md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 bg-white">
            <!-- Liens de navigation -->
            <LinkHeader page="Actualité" link="/article"/>
            <LinkHeader page="Forum" link="/forum"/>
            <LinkHeader page="Annonces" link="/announcement"/>
            <LinkHeader page="Contact" link="/contact"/>            
            <!-- Connexion ou Déconnexion en mode mobile -->
            <li class="mt-4 md:hidden">
              <a v-if="!authStore.isAuthenticated" href="/login" class="flex items-center space-x-2 text-black hover:text-blue-700 icon-connect">
                <UIcon name="material-symbols:person" class="w-5 h-5" />
                <span class="uppercase font-semibold ">Connexion</span>
              </a>
              <a v-else @click="logout" class="flex items-center space-x-2 text-black hover:text-blue-700" style="cursor: pointer;">
                <UIcon name="material-symbols:logout" class="w-5 h-5" />
                <span class="uppercase font-semibold">Se déconnecter</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Barre de recherche et icône utilisateur en mode desktop -->
        <div class="hidden md:flex items-center space-x-4 md:order-2">
          <!-- Barre de recherche visible en desktop -->
          <div class="relative md:ml-8">
            
            
          </div>

          <!-- Icône utilisateur visible en desktop uniquement -->
          <div class="flex items-center space-x-2">
            <span class="text-black font-semibold">|</span>
            <a v-if="!authStore.isAuthenticated" href="/login" style="display: flex;" class="icon-connect">
              <UIcon name="material-symbols:person" class="w-5 h-5 text-black" />
              <p class="text-black uppercase font-semibold ">Connexion</p>
            </a>
            <a v-else @click="logout" class="flex items-center space-x-2 text-black icon-connect" style="cursor: pointer;">
                <UIcon name="material-symbols:logout" class="w-5 h-5" />
                <span class="uppercase font-semibold">Se déconnecter</span>
              </a>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <slot >
    </slot>
  </main>

  <footer class="bg-black text-white">
    <div class="w-full max-w-screen-xl mx-auto p-4 md:py-8">
      <div class="sm:flex sm:items-start sm:justify-between">
        <!-- Logo et nom du site -->
        <a href="/" class="flex items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
          <img src="@/assets/img/logo_GameStaker_white.webp" class="h-20" alt="GameStackers Logo" />
        </a>

        <!-- Liens de navigation - section principale -->
        <div class="flex flex-col md:flex-row text-sm font-medium space-y-6 md:space-y-0 md:gap-12 self-end">
          <div class="flex flex-wrap gap-5">
            <LinkFooter page="Actualité" link="/article"/>
            <LinkFooter page="Forum" link="/forum"/>
            <LinkFooter page="Annonces" link="/annonces"/>
            <LinkFooter page="Contact" link="/contact"/>
            <LinkFooter page="Qui sommes-nous" link="/qui-sommes-nous"/>
            <LinkFooter page="Mentions légales" link="/mentions-légales"/>
          </div>
        </div>
      </div>

      <!-- Ligne de séparation -->
      <hr class="my-6 border-gray-700 sm:mx-auto lg:my-8" />
      <!-- Copyright -->
      <span class="block text-sm text-white sm:text-center">© 2024 GameStackers. Tous droits réservés.</span>
    </div>
  </footer>
  <SupportContact />
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import SupportContact from '@/components/SupportContact.vue';

const authStore = useAuthStore();
const isMenuOpen = ref(false);

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

const logout = () => {
  authStore.logout();
};
</script>

<style scoped>

.icon-connect {
  cursor: pointer;
  color: black;
}

.icon-connect:hover span,
.icon-connect:hover p {
  color: rgb(52, 211, 153);
}

.animate-marquee {
        display: inline-block;
        animation: marquee 15s linear infinite;
    }

    @keyframes marquee {
        from {
            transform: translateX(100%);
        }
        to {
            transform: translateX(-100%);
        }
    }
</style>