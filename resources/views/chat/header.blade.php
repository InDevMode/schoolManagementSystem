@php
    use Carbon\Carbon;

    $profilePicture = !empty($getReceiver->profile_picture)
        ? 'upload/profile/' . $getReceiver->profile_picture
        : 'upload/default.jpg';

    $isOnline = $getReceiver->last_login
        && Carbon::parse($getReceiver->last_login)->greaterThan(Carbon::now()->subMinutes(5));
@endphp

<header class="bg-white dark:bg-gray-700 px-4 py-2 text-gray-700 dark:text-gray-300 flex items-center justify-between">
    <div class="flex flex-col">
        <!-- Ligne image + nom/prénom -->
        <div class="flex items-center space-x-2">
            <img src="{{ $profilePicture }}" alt="Avatar" class="w-8 h-8 rounded-full">
            <span class="font-semibold text-md">{{ $getReceiver->last_name }} {{ $getReceiver->name }}</span>
        </div>

        <!-- Ligne statut / dernière connexion -->
        <em class="text-sm text-gray-500 flex items-center ms-8">
            @if($isOnline)
                <i class="fa-solid fa-circle text-emerald-400 mr-2"></i>En ligne
            @else
                Dernière connexion : {{ Carbon::parse($getReceiver->last_login)->format('d M Y à H:i') }}
            @endif
        </em>
    </div>

    <!-- Button to open sidebar on mobile -->
    <button class="xl:hidden" @click="openSidebar = !openSidebar">
        <iconify-icon icon="mdi:menu" width="24" height="24"></iconify-icon>
    </button>
</header>
