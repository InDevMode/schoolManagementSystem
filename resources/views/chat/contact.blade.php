@php
      use Carbon\Carbon;
      $isOnline = !empty($chatUser['last_login']) && Carbon::parse($chatUser['last_login'])->diffInMinutes(now()) <= 5;
@endphp

@foreach ($getChatUser as $chatUser)
      <div class="chat-preview flex items-center justify-between mb-4 cursor-pointer transition duration-300 ease-in-out mx-3 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600"
            onclick="window.location.href='{{ url('chat?receiver_id=' . base64_encode($chatUser['user_id'])) }}'">

            <div class="flex items-center">
                  <div class="relative w-12 h-12 bg-gray-300 rounded-full mr-3">
                        <img src="{{ $chatUser['sender_profile_picture'] }}" alt="User Profile"
                              class="w-12 h-12 rounded-full">
                        <span
                              class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white
                    {{ $isOnline ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                  </div>

                  <div class="flex-1">
                        <h2 class="text-lg dark:text-gray-300">
                              {{ $chatUser['name'] }}
                        </h2>

                        <p class="text-sm dark:text-gray-400">
                              {!! \Illuminate\Support\Str::limit($chatUser['message'], 40) !!}
                        </p>

                        <em class="text-sm text-gray-400">
                             En ligne {{ Carbon::parse($chatUser['created_date'])->locale('fr')->diffForHumans() }}
                        </em>
                  </div>
            </div>

            {{-- Badge pour le countMessage --}}
            @if (!empty($chatUser['countMessage']) && $chatUser['countMessage'] > 0)
                  <div class="ml-3">
                        <span
                              class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                              {{ $chatUser['countMessage'] }}
                        </span>
                  </div>
            @endif
      </div>
@endforeach
