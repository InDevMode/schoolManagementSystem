@extends('layouts.app')
@section('content')
      <header
            class="px-4 py-2 border-b border-gray-300 dark:border-gray-900 bg-indigo-600 dark:bg-gray-700 text-white flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Messages </h1>
            <p class="text-2xl font-semibold">@include('message') </p>
      </header>
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
                        <!-- Incoming Message -->
                        @include('chat.receiver')

                        <!-- Outgoing Message -->
                        @include('chat.sender')
                  </div>

                  <!-- Chat Input -->
                  @include('chat.input')
            </div>
      </div>
@endsection

<script></script>
