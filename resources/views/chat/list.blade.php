@extends('layouts.app')
@section('content')
      <div class="flex h-screen overflow-hidden" x-data="{ openSidebar: false, search: '' }">

            <!-- Sidebar -->
            <div class="bg-white dark:bg-gray-800 border-r-4 dark:border-gray-900 transform transition-transform duration-300
         fixed top-24 sm:top-0 bottom-0 left-0 w-64 z-40 xl:relative xl:translate-x-0 xl:w-1/4 xl:h-full"
                  :class="openSidebar ? 'translate-x-0' : '-translate-x-full'">
                  <!-- Sidebar Header -->
                  @include('chat.search')

                  <!-- Contact List -->
                  <div class="no-scrollbar overflow-y-auto h-screen px-0 mb-9 pb-20 border-b dark:border-gray-800">
                        @include('chat.contact')
                  </div>
            </div>

            <!-- Main Chat Area -->
            @if (!empty($getReceiver))
                  <div class="flex-1 flex flex-col">
                        <!-- Chat Header -->
                        @include('chat.header')

                        <!-- Chat Messages -->
                        <div
                              class="no-scrollbar flex-1 overflow-y-auto p-4 border-b dark:border-gray-800 bg-indigo-100/25 dark:bg-gray-900">
                              @include('chat.message')
                        </div>

                        <!-- Chat Input -->
                        @include('chat.input')
                  </div>
            @endif
      </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
