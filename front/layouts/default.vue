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
    <nav class="bg-white border-b border-black dark:bg-neutral-950">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <!-- Logo et Nom du site -->
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img
    :src="colorMode === 'dark' ? logoWhite : logoBlack"
    class="h-10 transition-all duration-300"
    :alt="colorMode === 'dark' ? 'Logo blanc GameStaker' : 'Logo noir GameStaker'"
  />
            </a>

            <div class="flex items-center space-x-3">
                <UIcon name="material-symbols:clear-day-rounded" class="w-5 h-5 text-amber-400 dark:text-white" />
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                    type="checkbox"
                    class="sr-only peer"
                    :checked="colorMode === 'dark'"
                    @change="toggleColorMode"
                    />
                    <div
                    class="w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-400 dark:bg-gray-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-500"
                    ></div>
                </label>
                    <UIcon name="material-symbols:mode-night" class="w-5 h-5 dark:text-amber-400" />
            </div>


            <!-- Bouton menu burger (visible en mode mobile) -->
            <button type="button" aria-controls="navbar-menu" aria-expanded="false"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                @click="toggleMenu">
                <span class="sr-only">Ouvrir le menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>

            <!-- Menu de navigation (visible en mode desktop et mobile) -->
            <div :class="['w-full uppercase font-semibold md:flex md:w-auto', isMenuOpen ? '' : 'hidden']" id="navbar-menu">
                <ul class="flex flex-col p-4 md:p-0 mt-4 font-semibold border border-gray-100 rounded-lg md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 bg-white dark:bg-neutral-950 ">
                    <!-- Liens de navigation -->
                    <LinkHeader page="Actualité" link="/article"/>
                    <LinkHeader page="Forum" link="/forum"/>
                    <LinkHeader page="Annonces" link="/announcement"/>
                    <LinkHeader page="Contact" link="/contact"/>
                    <!-- Connexion ou Déconnexion en mode mobile -->
                    <li class="mt-4 md:hidden">
                        <a v-if="!authStore.isAuthenticated" href="/login" class="flex items-center space-x-2 text-black hover:text-blue-700 icon-connect">
                            <UIcon name="material-symbols:person" class="w-5 h-5" />
                            <span class="uppercase font-semibold">Connexion</span>
                        </a>
                        <a v-else @click="logout" class="flex items-center space-x-2 text-black dark:text-white hover:text-blue-700" style="cursor: pointer;">
                            <UIcon name="material-symbols:logout" class="w-5 h-5" />
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Barre de recherche et icône utilisateur en mode desktop -->
            <div class="hidden md:flex items-center space-x-4 md:order-2">
                <!-- Icône utilisateur et lien vers le profil en mode desktop uniquement -->
                <div class="flex items-center space-x-2">
                    <span class="text-black font-semibold">|</span>
                    <a v-if="!authStore.isAuthenticated" href="/login" class="icon-connect">
                        <UIcon name="material-symbols:person" class="w-5 h-5 text-black dark:text-white" />
                        <p class="text-black uppercase font-semibold dark:text-white">Connexion</p>
                    </a>
                    <template v-else>
                        <a href="/profile" class="icon-connect">
                            <UIcon name="material-symbols:person" class="w-5 h-5 text-black dark:text-emerald-500" />
                        </a>
                        <a @click="logout" class="icon-connect" style="cursor: pointer;">
                            <UIcon name="material-symbols:logout" class="w-5 h-5" />
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</header>

<main>
    <slot></slot>
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
                    <LinkFooter page="Mentions légales" link="/mentions-legales"/>
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
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import SupportContact from '@/components/SupportContact.vue';
import logoBlack from '@/assets/img/logo_GameStaker_black.webp';
import logoWhite from '@/assets/img/logo_GameStaker_white.webp';


// Référence pour le mode
const isMenuOpen = ref(false);
const colorMode = ref(localStorage.getItem('colorMode') || 'light'); // Récupérer depuis localStorage

// Toggle pour le menu
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

// Gestion de la déconnexion
const logout = () => {
  authStore.logout();
  window.location.href = '/login';
};

// Fonction pour basculer entre clair et sombre
const toggleColorMode = () => {
  colorMode.value = colorMode.value === 'dark' ? 'light' : 'dark';
  localStorage.setItem('colorMode', colorMode.value); // Stocker dans localStorage
  document.documentElement.classList.toggle('dark', colorMode.value === 'dark');
};

// Appliquer le mode au montage
onMounted(() => {
  if (colorMode.value === 'dark') {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
});

const authStore = useAuthStore();
</script>




<style scoped>
.icon-connect {
    cursor: pointer;
    display: flex;
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
