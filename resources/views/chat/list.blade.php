@extends('layouts.app')
@section('content')
      <div class="flex h-screen overflow-hidden" x-data="{ openSidebar: false }">

            <!-- Sidebar -->
            <div class="bg-white dark:bg-gray-800 border-r-4 dark:border-gray-900 transform transition-transform duration-300
         fixed top-24 sm:top-0 bottom-0 left-0 w-64 z-40 xl:relative xl:translate-x-0 xl:w-1/4 xl:h-full"
                  :class="openSidebar ? 'translate-x-0' : '-translate-x-full'">
                  <!-- Sidebar Header -->
                  @include('chat.search')

                  <!-- Contact List -->
                  <div class="no-scrollbar overflow-y-auto h-screen px-0 mb-9 pb-20 border-b dark:border-gray-800">
                        @if (!empty($getReceiver))
                              @include('chat.contact')
                        @endif
                  </div>
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 flex flex-col">
                  <!-- Chat Header -->
                  @include('chat.header')

                  <!-- Chat Messages -->
                  <div class="no-scrollbar flex-1 overflow-y-auto p-4 pb-96 border-b dark:border-gray-800">
                        @include('chat.message')
                  </div>

                  <!-- Chat Input -->
                  @include('chat.input')
            </div>
      </div>
@endsection

<script>
      function markMessagesAsRead(receiverId) {
            fetch(`{{ url('/chat/read') }}`, {
                        method: 'POST',
                        headers: {
                              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                              'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                              receiver_id: receiverId
                        })
                  }).then(response => response.json())
                  .then(data => {
                        if (data.success) {
                              // Mise à jour visuelle du preview
                              const preview = document.querySelector(
                                    `.chat-preview[data-receiver-id="${base64_encode(receiverId)}"]`);
                              if (preview) {
                                    preview.classList.remove('bg-indigo-50', 'unread');
                              }

                              // Mise à jour des badges dans la fenêtre de chat
                              document.querySelectorAll('.status-badge').forEach(badge => {
                                    badge.textContent = 'Vu';
                                    badge.classList.remove('text-red-500');
                                    badge.classList.add('text-gray-400');
                              });
                        }
                  });
      }

      document.addEventListener('DOMContentLoaded', function() {
            markMessagesAsRead({{ $getReceiver->id }});
      });
</script>
