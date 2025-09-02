<form action="" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="receiver_id" value="{{ $getReceiver->id }}">
      <div class="bg-white dark:bg-black border-t border-gray-300 dark:border-black w-full">
            <div class="flex items-center py-2 px-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                  <button type="button"
                        class="inline-flex justify-center p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                        <iconify-icon icon="heroicons:photo-solid" width="24" height="24"></iconify-icon>
                  </button>
                  <button type="button"
                        class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                        <iconify-icon icon="heroicons:face-smile-solid" width="24" height="24"></iconify-icon>
                  </button>
                  <textarea id="message" rows="1" name="message" required="Veuillez entrez un message"
                        class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                        placeholder="Entrez votre message..."></textarea>
                  <button type="submit"
                        class="inline-flex justify-center p-2 text-indigo-600 rounded-full hover:bg-indigo-100 dark:text-indigo-500 dark:hover:bg-gray-600">
                        <iconify-icon icon="mdi:send" width="24" height="24" rotate="45deg"></iconify-icon>
                  </button>
            </div>
      </div>
</form>
