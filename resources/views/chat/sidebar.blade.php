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
            @foreach ($contacts as $contact)
                 <x-chat.contact :contact="$contact" :activeContactId="$activeContactId" />
            @endforeach
      </div>
</div>
