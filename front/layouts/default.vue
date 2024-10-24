<template>
  <header>
    <nav class="bg-white border-gray-200">
      <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <!-- Logo et Nom du site -->
        <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
          <img src="@/assets/img/logo_GameStaker_black.webp" class="h-10" alt="GameStaker Logo" />
        </a>

        <!-- Bouton menu burger (visible en mode mobile) -->
        <button
          type="button"
          aria-controls="navbar-menu"
          aria-expanded="false"
          class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
          @click="toggleMenu"
        >
          <span class="sr-only">Open main menu</span>
          <svg
            class="w-5 h-5"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 17 14"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M1 1h15M1 7h15M1 13h15"
            />
          </svg>
        </button>

        <!-- Menu de navigation (visible en mode desktop et mobile) -->
        <div :class="['w-full  uppercase  font-semibold		md:flex md:w-auto', isMenuOpen ? '' : 'hidden'] " id="navbar-menu">
          <ul
            class="flex flex-col p-4 md:p-0 mt-4 font-semibold	 border border-gray-100 rounded-lg md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 bg-white"
          >
            <!-- Liens de navigation -->
            <li>
              <a href="#" class="block py-2 px-3 text-black hover:text-blue-700 rounded md:bg-transparent md:p-0">Actualité</a>
            </li>
            <li>
              <a href="#" class="block py-2 px-3 text-black hover:text-blue-700 rounded md:bg-transparent md:p-0">Forum</a>
            </li>
            <li>
              <a href="#" class="block py-2 px-3 text-black hover:text-blue-700 rounded md:bg-transparent md:p-0">Annonces</a>
            </li>

            <!-- Barre de recherche en mode mobile -->
            <li class="mt-4 md:hidden">
              <div class="relative">
                <input
                  type="text"
                  id="search-navbar-mobile"
                  class="block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Search..."
                />
                <span class="absolute right-3 top-2.5 text-gray-500">
                  <UIcon name="material-symbols:search-rounded" class="w-5 h-5 text-gray-500" />
                </span>
              </div>
            </li>

            <!-- Connexion (Visible dans le menu mobile) -->
            <li class="mt-4 md:hidden">
              <a href="#" class="flex items-center space-x-2 text-black hover:text-blue-700">
                <UIcon name="material-symbols:person" class="w-5 h-5" />
                <span class="uppercase font-semibold		">Connexion </span> 
              </a>
            </li>
          </ul>
        </div>

        <!-- Barre de recherche et icône utilisateur en mode desktop -->
        <div class="hidden md:flex items-center space-x-4 md:order-2">
          <!-- Barre de recherche visible en desktop -->
          <div class="relative md:ml-8">
            <input
              type="text"
              id="search-navbar"
              class="block p-2 ps-5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500"
              placeholder="Search..."
            />
            <span class="absolute right-3 top-2.5 text-gray-500">
              <UIcon name="material-symbols:search-rounded" class="w-5 h-5 text-gray-500" />
            </span>
          </div>

          <!-- Icône utilisateur visible en desktop uniquement -->
          <div class="flex items-center space-x-2"><span class="text-black font-semibold	">|</span>
            <UIcon name="material-symbols:person" class="w-5 h-5 text-black" />
            <p class="text-black uppercase font-semibold		"> Connexion</p> 
          </div>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <slot>
    </slot>
  </main>

  

<footer class="bg-black text-white ">
    <div class="w-full max-w-screen-xl mx-auto p-4 md:py-8">
        <div class="sm:flex sm:items-start sm:justify-between ">
            <!-- Logo et nom du site -->
            <a href="/" class="flex items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
                <img src="@/assets/img/logo_GameStaker_white.webp" class="h-20" alt="GameStackers Logo" />
            </a>

            <!-- Liens de navigation - section principale -->
            <div class="flex flex-col md:flex-row text-sm font-medium space-y-6 md:space-y-0 md:gap-12 self-end ">
                <div class="flex flex-wrap  gap-5">
                    <a href="#" class="hover:underline block">Actualité</a>
                    <a href="#" class="hover:underline block">Forum</a>
                    <a href="#" class="hover:underline block">Annonces</a>

                    <a href="#" class="hover:underline block">Contact</a>
                    <a href="#" class="hover:underline block">Qui sommes-nous</a>
                    <a href="#" class="hover:underline block">Mentions légales</a>
                </div>
            </div>
        </div>

        <!-- Ligne de séparation -->
        <hr class="my-6 border-gray-700 sm:mx-auto lg:my-8" />

        <!-- Copyright -->
        <span class="block text-sm text-white sm:text-center">© 2024 GameStackers. Tous droits réservés.</span>
    </div>
</footer>



</template>

<script>
export default {
  data() {
    return {
      isMenuOpen: false,
    };
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
  },
};
</script>
