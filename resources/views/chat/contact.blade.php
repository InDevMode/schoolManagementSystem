@php
    use Carbon\Carbon;

    // Photo de profil
    $profilePicture = !empty($getReceiver->profile_picture)
        ? 'upload/profile/' . $getReceiver->profile_picture
        : 'upload/default.jpg';

    // Exemple de flags (tu devras les adapter à ton modèle) {{ $isUnread ? 'font-bold' : 'font-normal' }}
    // $isUnread = !$message->is_read; // true si non lu {{ $isActive ? 'bg-gray-200' : '' }}
    // $isActive = isset($activeMessageId) && $activeMessageId === $message->id; // true si message ouvert {{ $isUnread ? 'font-bold text-gray-800' : 'font-normal text-gray-600' }}
@endphp

<div
    class="flex items-center mb-4 cursor-pointer transition duration-300 ease-in-out mx-3 p-2 rounded-md
    hover:bg-gray-100
    ">

    <div class="w-12 h-12 bg-gray-300 rounded-full mr-3">
        <img src="{{ $profilePicture }}" alt="User Profile" class="w-12 h-12 rounded-full">
    </div>

    <div class="flex-1">
        <h2 class="text-lg ">
            {{ $getReceiver->last_name }} {{ $getReceiver->name }}
        </h2>
        <p class="">
           {{ Carbon::parse($getReceiver->created_at)->format('d M Y à H:i:s') }}
        </p>
    </div>
</div>
