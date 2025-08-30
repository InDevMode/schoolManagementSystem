@extends('layouts.app')
@section('content')
      <div class="flex h-screen overflow-hidden" x-data="{ openSidebar: false }" >
            <!-- Sidebar -->
            <div class="bg-white dark:bg-gray-800 border-r-4 dark:border-gray-900 transform transition-transform duration-300
         fixed top-24 sm:top-0 bottom-0 left-0 w-64 z-40 xl:relative xl:translate-x-0 xl:w-1/4 xl:h-full"
                  :class="openSidebar ? 'translate-x-0' : '-translate-x-full'">
                  <!-- Sidebar Header -->
                  <header
                        class="p-4 border-b border-gray-300 dark:border-gray-900 bg-indigo-600 dark:bg-gray-700 text-white flex justify-between items-center">
                        <h1 class="text-2xl font-semibold">Messages</h1>
                  </header>

                  <!-- Contact List -->
                  <div class="no-scrollbar overflow-y-auto h-screen px-0 mb-9 pb-20 border-b dark:border-gray-800">
                        <div class="flex items-center mb-4 cursor-pointer hover:bg-gray-100 p-2 rounded-md">
                              <div class="w-12 h-12 bg-gray-300 rounded-full mr-3">
                                    <img src="https://placehold.co/200x/ffa8e4/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato"
                                          alt="User Avatar" class="w-12 h-12 rounded-full">
                              </div>
                              <div class="flex-1">
                                    <h2 class="text-lg font-semibold">Alice</h2>
                                    <p class="text-gray-600">Hoorayy!!</p>
                              </div>
                        </div>

                        <div class="flex items-center mb-4 cursor-pointer hover:bg-gray-100 p-2 rounded-md">
                              <div class="w-12 h-12 bg-gray-300 rounded-full mr-3">
                                    <img src="https://placehold.co/200x/ad922e/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato"
                                          alt="User Avatar" class="w-12 h-12 rounded-full">
                              </div>
                              <div class="flex-1">
                                    <h2 class="text-lg font-semibold">Martin</h2>
                                    <p class="text-gray-600">That pizza place was amazing! 🍕</p>
                              </div>
                        </div>
                  </div>
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 flex flex-col">
                  <!-- Chat Header -->
                  <header
                        class="bg-white dark:bg-gray-700 p-4 text-gray-700 dark:text-gray-300 flex items-center justify-between">
                        <h1 class="text-2xl font-semibold">Alice</h1>
                        <!-- Button to open sidebar on mobile -->
                        <button class="xl:hidden" @click="openSidebar = !openSidebar">
                              <iconify-icon icon="mdi:menu" width="24" height="24"></iconify-icon>
                        </button>

                  </header>

                  <!-- Chat Messages -->
                  <div class="no-scrollbar flex-1 overflow-y-auto p-4 pb-96 border-b dark:border-gray-800">
                        <!-- Incoming Message -->
                        <div class="flex mb-4 cursor-pointer">
                              <div class="w-9 h-9 rounded-full flex items-center justify-center mr-2">
                                    <img src="https://placehold.co/200x/ffa8e4/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato"
                                          alt="User Avatar" class="w-8 h-8 rounded-full">
                              </div>
                              <div class="flex max-w-96 bg-white rounded-lg p-3 gap-3">
                                    <p class="text-gray-700">Hey Bob, how's it going?</p>
                              </div>
                        </div>

                        <!-- Outgoing Message -->
                        <div class="flex justify-end mb-4 cursor-pointer">
                              <div class="flex max-w-96 bg-indigo-500 text-white rounded-lg p-3 gap-3">
                                    <p>Hi Alice! I'm good, just finished a great book. How about you?</p>
                              </div>
                              <div class="w-9 h-9 rounded-full flex items-center justify-center ml-2">
                                    <img src="https://placehold.co/200x/b7a8ff/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato"
                                          alt="My Avatar" class="w-8 h-8 rounded-full">
                              </div>
                        </div>
                  </div>

                  <!-- Chat Input -->
                  <footer class="bg-white dark:bg-black border-t border-gray-300 dark:border-black w-full">
                        <div class="flex items-center py-2 px-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                              <button type="button"
                                    class="inline-flex justify-center p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                                    <iconify-icon icon="heroicons:photo-solid" width="24" height="24"></iconify-icon>
                              </button>
                              <button type="button"
                                    class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                                    <iconify-icon icon="heroicons:face-smile-solid" width="24"
                                          height="24"></iconify-icon>
                              </button>
                              <textarea id="chat" rows="1"
                                    class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Your message..."></textarea>
                              <button type="submit"
                                    class="inline-flex justify-center p-2 text-blue-600 rounded-full hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600">
                                    <iconify-icon icon="mdi:send" width="24" height="24" rotate="45deg"></iconify-icon>
                              </button>
                        </div>
                  </footer>
            </div>
      </div>
@endsection

<script></script>
