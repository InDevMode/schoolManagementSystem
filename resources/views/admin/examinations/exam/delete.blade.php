<div x-data="{ openModal: false, deleteId: null, deleteName: '' }">
    <!-- Modal -->
    <div x-show="openModal"
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Confirmation</h2>
            <p class="text-gray-600">Êtes-vous sûr de vouloir supprimer <strong x-text="deleteName"></strong> ?</p>

            <!-- Buttons -->
            <div class="mt-4 flex justify-end space-x-3">
                <button 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Annuler
                </button>

                <!-- ✅ Correction : Utilisation de x-bind:href -->
                <a :href=""
                   class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Oui
                </a>
            </div>
        </div>
    </div>
</div>
