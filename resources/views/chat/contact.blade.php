@php
      use Carbon\Carbon;

      $currentReceiverId = request()->query('receiver_id');
@endphp
<div>
      @forelse ($getChatUser as $chatUser)
            @php
                  $encodedId = base64_encode($chatUser['user_id']);
                  $isActive = $currentReceiverId === $encodedId;

                  $isOnline =
                      !empty($chatUser['last_login']) &&
                      Carbon::parse($chatUser['last_login'])->diffInMinutes(now()) <= 5;
                  $lastSeen = Carbon::parse($chatUser['last_login'])->locale('fr')->diffForHumans();
                  $profilePicture = !empty($chatUser['sender_profile_picture'])
                      ? asset($chatUser['sender_profile_picture'])
                      : asset('upload/default.jpg');
            @endphp

            <div x-data="{ name: '{{ strtolower($chatUser['name']) }}', message: '{{ strtolower($chatUser['message']) }}' }"
                  x-show="search === '' || name.includes(search.toLowerCase()) || message.includes(search.toLowerCase())"
                  class="chat-preview flex items-center justify-between mb-4 cursor-pointer transition duration-300 ease-in-out mx-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600
                   {{ $isActive ? 'bg-indigo-100 dark:bg-gray-600' : '' }}"
                  onclick="
                      if (typeof resetChatEditing === 'function') {
                          resetChatEditing();
                      }
                      window.location.href='{{ url('chat?receiver_id=' . $encodedId) }}'
                  ">

                  <div class="flex items-center">
                        <div class="relative w-12 h-12 bg-gray-300 rounded-full mr-3">
                              <img src="{{ $profilePicture }}" alt="User Profile" class="w-12 h-12 rounded-full">
                              <span
                                    class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white {{ $isOnline ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        </div>

                        <div class="flex-1">
                              <h2 class="text-lg dark:text-gray-300">{{ $chatUser['name'] }}</h2>
                              @if ($chatUser['is_delete'])
                                    <span
                                          class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                          Message Supprimé
                                    </span>
                              @else
                                    <p class="text-sm dark:text-gray-400">{!! \Illuminate\Support\Str::limit($chatUser['message'], 40) !!}</p>
                              @endif
                              <em class="text-xs text-gray-400 flex items-center">
                                    {{ $isOnline ? 'En ligne' : 'En ligne ' . $lastSeen }}
                              </em>
                        </div>
                  </div>

                  @if (!empty($chatUser['countMessage']) && $chatUser['countMessage'] > 0)
                        <div class="ml-3">
                              <span
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                    {{ $chatUser['countMessage'] }}
                              </span>
                        </div>
                  @endif
            </div>
      @empty
            <p class="text-center text-gray-500 dark:text-gray-400 mt-4">Aucun contact disponible.</p>
      @endforelse
</div>
