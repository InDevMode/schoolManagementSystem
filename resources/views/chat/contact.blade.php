@php
      use Carbon\Carbon;

    //   $receiver = $getChatUser;
    //   $lastMessage = $getChats->last(); // Le dernier message dans la conversation
    //   $isUnread = $lastMessage && $lastMessage->status == 0 && $lastMessage->receiver_id == Auth::id();
    //   $isOnline =
    //       $receiver->last_login && Carbon::parse($receiver->last_login)->greaterThan(Carbon::now()->subMinutes(5));

      $profilePicture = !empty($receiver->profile_picture)
          ? 'upload/profile/' . $receiver->profile_picture
          : 'upload/default.jpg';
@endphp

@foreach ($getChatUser as $chatUser)
      <div class="chat-preview flex items-center mb-4 cursor-pointer transition duration-300 ease-in-out mx-3 p-2 rounded-md hover:bg-gray-100">
            <div class="relative w-12 h-12 bg-gray-300 rounded-full mr-3">
                  <img src="{{ $profilePicture }}" alt="User Profile" class="w-12 h-12 rounded-full">
                  {{-- @if ($isOnline)
                        <span
                              class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                  @endif --}}
            </div>

            <div class="flex-1">
                  <h2 class="text-lg ">
                        {{ $chatUser['name'] }}
                  </h2>
                  {{-- @if ($lastMessage) --}}
                        <p class="text-sm text-gray-500">
                              {!! \Illuminate\Support\Str::limit($chatUser['message'], 40) !!}
                              @if ($chatUser['status'] == 0)
                                    <span class="text-red-500">Non lu</span>
                              @endif
                        </p>
                        <em class="text-sm text-gray-400">
                              {{ Carbon::parse($chatUser['created_date'])->format('d M Y à H:i') }}
                        </em>
                  {{-- @endif --}}
            </div>
      </div>
@endforeach
