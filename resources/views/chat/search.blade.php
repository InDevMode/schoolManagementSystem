<!-- Champ de recherche -->
<form action="" method="GET" @submit.prevent class="relative w-full p-2">
      <!-- Icône loupe -->
      <span class="absolute top-1/2 left-5 -translate-y-1/2 text-gray-400 dark:text-gray-400 pointer-events-none text-sm">
            <i class="fa-solid fa-magnifying-glass"></i>
      </span>

      <!-- Input de recherche -->
      <input type="text" x-model="search" placeholder="Rechercher un message ou un utilisateur..."
            class="appearance-none w-full text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-600 focus:border-indigo-600 py-2.5 pr-12 pl-10 outline-none" />

      <!-- Bouton clear -->
      <button type="button" x-show="search.length > 0" @click="search = ''"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">
            <i class="fa-solid fa-xmark"></i>
      </button>
</form>
