@php
      use Carbon\Carbon;
      use Illuminate\Support\Str;
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

            $hasFile = !empty($chat->file);
            $fileUrl = $hasFile ? asset('upload/chats/' . $chat->file) : null;
      @endphp

      {{-- Messages envoyés par moi --}}
      @if ($isSender)
            <div class="flex justify-end mb-2 items-end">
                  <div class="flex flex-col items-end max-w-96">
                        {{-- 📎 Fichier joint au-dessus --}}
                        @if ($hasFile)
                              <div class="mb-2">
                                    @if (Str::endsWith($chat->file, ['.jpg', '.jpeg', '.png', '.gif']))
                                          {{-- Image miniature cliquable pour téléchargement --}}
                                          <a href="{{ $fileUrl }}" download>
                                                <img src="{{ $fileUrl }}" alt="Image jointe"
                                                      class="w-20 h-20 object-cover rounded shadow border hover:opacity-80 cursor-pointer">
                                          </a>
                                    @elseif (Str::endsWith($chat->file, ['.pdf']))
                                          {{-- Icône PDF --}}
                                          <a href="{{ $fileUrl }}" download
                                                class="flex items-center space-x-2 text-red-600 hover:underline">
                                                <iconify-icon icon="mdi:file-pdf-box" class="text-red-600" width="28"
                                                      height="28"></iconify-icon>
                                                <span class="text-sm">Télécharger le PDF</span>
                                          </a>
                                    @elseif (Str::endsWith($chat->file, ['.doc', '.docx']))
                                          {{-- Icône Word --}}
                                          <a href="{{ $fileUrl }}" download
                                                class="flex items-center space-x-2 text-blue-600 hover:underline">
                                                <iconify-icon icon="mdi:microsoft-word" class="text-blue-600"
                                                      width="28" height="28"></iconify-icon>
                                                <span class="text-sm">Télécharger le document Word</span>
                                          </a>
                                    @elseif (Str::endsWith($chat->file, ['.xls', '.xlsx']))
                                          {{-- Icône Excel --}}
                                          <a href="{{ $fileUrl }}" download
                                                class="flex items-center space-x-2 text-green-600 hover:underline">
                                                <iconify-icon icon="mdi:microsoft-excel" class="text-green-600"
                                                      width="28" height="28"></iconify-icon>
                                                <span class="text-sm">Télécharger le fichier Excel</span>
                                          </a>
                                    @else
                                          {{-- Icône générique --}}
                                          <a href="{{ $fileUrl }}" download
                                                class="flex items-center space-x-2 text-gray-600 hover:underline">
                                                <iconify-icon icon="mdi:file" class="text-gray-600" width="28"
                                                      height="28"></iconify-icon>
                                                <span class="text-sm">Télécharger le fichier</span>
                                          </a>
                                    @endif
                              </div>
                        @endif

                        {{-- 💬 La bulle du message --}}
                        <div class="relative bubble-right bg-indigo-500 text-white rounded-full px-5 py-2">
                              <p class="text-sm">{!! nl2br(e($chat->message)) !!}</p>
                        </div>

                        {{-- ⏱️ Horodatage --}}
                        <div class="text-[10px] text-right dark:text-gray-300 text-gray-700 mb-3 pr-2">
                              {{ $chat->created_date->locale('fr')->diffForHumans() }}
                              @if ($chat->status == 1)
                                    <em class="ml-2 text-emerald-500 font-semibold">Vu</em>
                              @else
                                    <em class="ml-2 text-gray-400 font-semibold">Envoyé</em>
                              @endif
                        </div>
                  </div>

                  {{-- Avatar --}}
                  <div class="w-9 h-9 rounded-full flex items-center justify-center ml-2">
                        <img src="{{ asset($profilePicture) }}" alt="My Avatar" class="w-8 h-8 rounded-full">
                  </div>
            </div>
      @else
            {{-- Messages reçus --}}
            <div class="flex mb-2 items-end">
                  {{-- Avatar --}}
                  <div class="w-9 h-9 rounded-full flex items-center justify-center mr-2">
                        <img src="{{ asset($profilePicture) }}" alt="User Avatar" class="w-8 h-8 rounded-full">
                  </div>

                  <div class="flex flex-col items-start max-w-xs">
                        {{-- 📎 Fichier joint --}}
                        @if ($hasFile)
                              <div class="mb-2">
                                    @if (Str::endsWith($chat->file, ['.jpg', '.jpeg', '.png', '.gif']))
                                          {{-- Image miniature cliquable pour téléchargement --}}
                                          <a href="{{ $fileUrl }}" download>
                                                <img src="{{ $fileUrl }}" alt="Image jointe"
                                                      class="w-20 h-20 object-cover rounded shadow border hover:opacity-80 cursor-pointer">
                                          </a>
                                    @elseif (Str::endsWith($chat->file, ['.pdf']))
                                          {{-- Icône PDF --}}
                                          <a href="{{ $fileUrl }}" download
                                                class="flex items-center space-x-2 text-red-600 hover:underline">
                                                <iconify-icon icon="mdi:file-pdf-box" class="text-red-600"
                                                      width="28" height="28"></iconify-icon>
                                                <span class="text-sm">Télécharger le PDF</span>
                                          </a>
                                    @elseif (Str::endsWith($chat->file, ['.doc', '.docx']))
                                          {{-- Icône Word --}}
                                          <a href="{{ $fileUrl }}" download
                                                class="flex items-center space-x-2 text-blue-600 hover:underline">
                                                <iconify-icon icon="mdi:microsoft-word" class="text-blue-600"
                                                      width="28" height="28"></iconify-icon>
                                                <span class="text-sm">Télécharger le document Word</span>
                                          </a>
                                    @elseif (Str::endsWith($chat->file, ['.xls', '.xlsx']))
                                          {{-- Icône Excel --}}
                                          <a href="{{ $fileUrl }}" download
                                                class="flex items-center space-x-2 text-green-600 hover:underline">
                                                <iconify-icon icon="mdi:microsoft-excel" class="text-green-600"
                                                      width="28" height="28"></iconify-icon>
                                                <span class="text-sm">Télécharger le fichier Excel</span>
                                          </a>
                                    @else
                                          {{-- Icône générique --}}
                                          <a href="{{ $fileUrl }}" download
                                                class="flex items-center space-x-2 text-gray-600 hover:underline">
                                                <iconify-icon icon="mdi:file" class="text-gray-600" width="28"
                                                      height="28"></iconify-icon>
                                                <span class="text-sm">Télécharger le fichier</span>
                                          </a>
                                    @endif
                              </div>
                        @endif


                        {{-- 💬 La bulle du message --}}
                        <div class="relative bubble-left bg-gray-300 text-gray-800 rounded-full px-5 py-2">
                              <p class="text-sm">{!! nl2br(e($chat->message)) !!}</p>
                        </div>

                        {{-- ⏱️ Horodatage --}}
                        <div class="text-[10px] text-left dark:text-gray-300 text-gray-700 mb-3 pl-2">
                              {{ $chat->created_date->locale('fr')->diffForHumans() }}
                              @if ($isUnread)
                                    <em class="ml-2 text-red-500 font-semibold">Non lu</em>
                              @else
                                    <em class="ml-2 text-gray-300 font-semibold">Lu</em>
                              @endif
                        </div>
                  </div>
            </div>
      @endif

@endforeach
