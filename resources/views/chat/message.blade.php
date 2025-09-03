@php
      use Carbon\Carbon;
      $previousDate = null;
@endphp

@foreach ($getChats as $chat)
      @php
            $isSender = $chat->sender_id == Auth::id();
            $user = $isSender ? Auth::user() : $getReceiver;

            $profilePicture = !empty($user->profile_picture)
                ? 'upload/profile/' . $user->profile_picture
                : 'upload/default.jpg';

            $messageDate = Carbon::parse($chat->created_date);
            $currentDate = $messageDate->format('Y-m-d');

            // Séparateur de date
            if ($previousDate !== $currentDate) {
                $label = $messageDate->isToday()
                    ? 'Aujourd’hui'
                    : ($messageDate->isYesterday()
                        ? 'Hier'
                        : $messageDate->translatedFormat('d F Y'));

                echo "<div class='text-center text-xs text-gray-500 my-4 font-semibold'>$label</div>";
                $previousDate = $currentDate;
            }

            $isUnread = $chat->status == 0 && $chat->receiver_id == Auth::id();
      @endphp

      {{-- Messages envoyés par moi --}}
      @if ($isSender)
            <div class="flex justify-end mb-2 items-end">
                  <div class="relative bubble-right bg-indigo-500 text-white rounded-full px-5 py-2 max-w-96">
                        <p class="text-sm">{!! nl2br(e($chat->message)) !!}</p>
                  </div>
                  <div class="w-9 h-9 rounded-full flex items-center justify-center ml-2">
                        <img src="{{ asset($profilePicture) }}" alt="My Avatar" class="w-8 h-8 rounded-full">
                  </div>
            </div>
            <div class="text-[10px] text-right dark:text-gray-300 text-gray-700 mb-3 pr-16">
                  {{ $chat->created_date->locale('fr')->diffForHumans() }}
                  @if ($chat->status == 1)
                        <em class="ml-2 text-emerald-500 font-semibold">Vu</em>
                  @else
                        <em class="ml-2 text-gray-400 font-semibold">Envoyé</em>
                  @endif
            </div>

            {{-- Messages reçus --}}
      @else
            <div class="flex mb-2 items-end">
                  <div class="w-9 h-9 rounded-full flex items-center justify-center mr-2">
                        <img src="{{ asset($profilePicture) }}" alt="User Avatar" class="w-8 h-8 rounded-full">
                  </div>
                  <div class="relative bubble-left bg-gray-300 text-gray-800 rounded-full px-5 py-2 max-w-96">
                        <p class="text-sm">{!! nl2br(e($chat->message)) !!}</p>
                  </div>
            </div>
            <div class="text-[10px] text-left dark:text-gray-300 text-gray-700  mb-3 pl-16">
                  {{ $chat->created_date->locale('fr')->diffForHumans() }}
                  @if ($isUnread)
                        <em class="ml-2 text-red-500 font-semibold">Non lu</em>
                  @else
                        <em class="ml-2 text-gray-300 font-semibold">Lu</em>
                  @endif
            </div>
      @endif
@endforeach
