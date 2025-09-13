@php
      use Carbon\Carbon;

      $profilePicture = !empty($getReceiver->profile_picture)
          ? asset('upload/profile/' . $getReceiver->profile_picture)
          : asset('upload/default.jpg');

      $isOnline = $getReceiver->last_login && Carbon::parse($getReceiver->last_login)->diffInMinutes(now()) <= 5;
      $lastSeen = Carbon::parse($getReceiver->last_login)->locale('fr')->diffForHumans();
@endphp

<header
      class="bg-indigo-500/25 dark:bg-gray-700 px-4 py-2 text-indigo-800 dark:text-gray-300 flex items-center justify-between">
      <div class="flex items-center space-x-3">
            <!-- Avatar avec point de statut positionné -->
            <div class="relative w-10 h-10 bg-gray-300 rounded-full">
                  <img src="{{ $profilePicture }}" alt="Avatar" class="w-10 h-10 rounded-full">
                  <span
                        class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white
                         {{ $isOnline ? 'bg-emerald-500' : 'bg-gray-400' }}">
                  </span>
            </div>

            <!-- Nom + statut -->
            <div class="flex flex-col">
                  <span class="font-semibold text-md">
                        {{ $getReceiver->last_name }} {{ $getReceiver->name }}
                  </span>
                  <span class="text-sm italic flex items-center text-indigo-800 dark:text-gray-300">

                        {{ $isOnline ? 'En ligne' : 'En ligne ' . $lastSeen }}
                  </span>
            </div>
      </div>

      <!-- Bouton menu mobile -->
      <button class="xl:hidden" @click="openSidebar = !openSidebar">
            <iconify-icon icon="mdi:menu" width="24" height="24"></iconify-icon>
      </button>
</header>
