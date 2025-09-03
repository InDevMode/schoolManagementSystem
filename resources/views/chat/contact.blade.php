@php
      $currentReceiverId = request()->query('receiver_id');
@endphp

<template x-for="user in {{ json_encode($getChatUser) }}" :key="user.user_id">
      <div x-show="
            user.name.toLowerCase().includes(search.toLowerCase()) ||
            user.last_name.toLowerCase().includes(search.toLowerCase()) ||
            user.message.toLowerCase().includes(search.toLowerCase())
        "
            class="chat-preview flex items-center justify-between mb-4 cursor-pointer transition duration-300 ease-in-out mx-3 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600"
            :class="{ 'bg-indigo-100 dark:bg-gray-600': '{{ $currentReceiverId }}' === btoa(user.user_id) }"
            @click="window.location.href='{{ url('chat?receiver_id=') }}' + btoa(user.user_id)">

            <!-- Avatar et statut en ligne -->
            <div class="flex items-center">
                  <div class="relative w-12 h-12 bg-gray-300 rounded-full mr-3">
                        <img :src="user.sender_profile_picture" alt="User Profile" class="w-12 h-12 rounded-full">
                        <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white"
                              :class="user.last_login && (new Date() - new Date(user.last_login)) / 60000 <= 5 ?
                                  'bg-emerald-500' : 'bg-gray-400'">
                        </span>
                  </div>

                  <!-- Nom et dernier message -->
                  <div class="flex-1">
                        <h2 class="text-lg dark:text-gray-300" x-text="user.name"></h2>
                        <p class="text-sm dark:text-gray-400" x-text="user.message.substring(0, 40)"></p>
                        <em class="text-xs text-gray-400"
                              x-text="new Date(user.created_date).toLocaleString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute:'2-digit' })"></em>
                  </div>
            </div>

            <!-- Badge messages non lus -->
            <template x-if="user.countMessage > 0">
                  <div class="ml-3">
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full"
                              x-text="user.countMessage">
                        </span>
                  </div>
            </template>
      </div>
</template>
