<div id="popup-modal" tabindex="-1"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                    data-modal-hide="popup-modal">
                <span>
                    <i class="fa-solid fa-2x fa-xmark"></i>
                </span>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-4 md:p-5 text-center">
                <span class="mx-auto mb-8 text-gray-400 w-12 h-12">
                    <i class="fa-solid fa-4x fa-circle-exclamation"></i>
                </span>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                    Êtes-vous sûr de vouloir supprimer cette assignation ?
                </h3>
                <a href="{{ url('admin/assign_subject/delete', $classSubject->id) }}" class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center
                mr-2">
                Oui, supprimer
                </a>
                <button data-modal-hide="popup-modal" type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>


