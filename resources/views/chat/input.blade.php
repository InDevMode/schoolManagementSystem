<!-- Alpine.js + Emoji Picker Element -->
<form action="" method="POST" enctype="multipart/form-data" x-data="{ fileName: '', filePreview: '', fileType: '', showEmoji: false }">
      {{ csrf_field() }}
      <input type="hidden" name="receiver_id" value="{{ $getReceiver->id }}">

      <div class="bg-white dark:bg-black border-t border-gray-300 dark:border-black w-full">
            <div class="flex items-center py-2 px-3 bg-gray-50 rounded-lg dark:bg-gray-700 relative">

                  <!-- 📎 Bouton fichier -->
                  <button type="button" @click="$refs.fileInput.click()"
                        class="inline-flex justify-center p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                        <iconify-icon icon="heroicons:photo-solid" width="24" height="24"></iconify-icon>
                  </button>

                  <!-- Input file caché -->
                  <input type="file" name="file" x-ref="fileInput" class="hidden"
                        @change="
                            const file = $refs.fileInput.files[0];
                            fileName = file?.name || '';
                            fileType = file?.type || '';
                            if (fileType.startsWith('image/') || fileType === 'application/pdf') {
                                const reader = new FileReader();
                                reader.onload = e => filePreview = e.target.result;
                                reader.readAsDataURL(file);
                            } else {
                                filePreview = '';
                            }
                            ">

                  <!-- Aperçu fichier -->
                  <div x-show="fileName" class="absolute bottom-20 left-10 z-50">
                        <!-- 🖼️ Aperçu image -->
                        <template x-if="fileType.startsWith('image/')">
                              <img :src="filePreview" alt="Preview"
                                    class="w-32 h-32 object-cover rounded shadow-lg border border-gray-300 dark:border-gray-600">
                        </template>

                        <!-- 📑 Aperçu PDF -->
                        <template x-if="fileType === 'application/pdf'">
                              <embed :src="filePreview" type="application/pdf"
                                    class="w-64 h-64 rounded shadow-lg border border-gray-300 dark:border-gray-600" />
                        </template>

                        <!-- 📄 Word -->
                        <template
                              x-if="fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || fileType === 'application/msword'">
                              <div class="flex items-center space-x-2 bg-white dark:bg-gray-800 p-3 rounded shadow">
                                    <iconify-icon icon="mdi:microsoft-word" class="text-blue-600" width="28"
                                          height="28"></iconify-icon>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                          x-text="fileName"></span>
                              </div>
                        </template>

                        <!-- 📊 Excel -->
                        <template
                              x-if="fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || fileType === 'application/vnd.ms-excel'">
                              <div class="flex items-center space-x-2 bg-white dark:bg-gray-800 p-3 rounded shadow">
                                    <iconify-icon icon="mdi:microsoft-excel" class="text-green-600" width="28"
                                          height="28"></iconify-icon>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                          x-text="fileName"></span>
                              </div>
                        </template>

                        <!-- 📄 Autres fichiers -->
                        <template
                              x-if="!fileType.startsWith('image/') && fileType !== 'application/pdf'
                     && fileType !== 'application/msword' && fileType !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                     && fileType !== 'application/vnd.ms-excel' && fileType !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'">
                              <div class="flex items-center space-x-2 bg-white dark:bg-gray-800 p-3 rounded shadow">
                                    <iconify-icon icon="mdi:file" class="text-gray-600" width="28"
                                          height="28"></iconify-icon>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                          x-text="fileName"></span>
                              </div>
                        </template>
                  </div>


                  <!-- 😄 Bouton emoji -->
                  <button type="button" @click="showEmoji = !showEmoji"
                        class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                        <iconify-icon icon="heroicons:face-smile-solid" width="24" height="24"></iconify-icon>
                  </button>

                  <!-- Emoji Picker -->
                  <div x-show="showEmoji" @click.outside="showEmoji = false"
                        class="absolute bottom-20 right-0 z-50 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg p-2 w-72">
                        <emoji-picker @emoji-click="(e) => $refs.message.value += e.detail.unicode"></emoji-picker>
                  </div>

                  <!-- 💬 Zone de texte -->
                  <textarea id="message" rows="1" name="message" x-ref="message" required
                        class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                        placeholder="Entrez votre message..."></textarea>

                  <!-- 📤 Bouton envoyer -->
                  <button type="submit"
                        class="inline-flex justify-center p-2 text-indigo-600 rounded-full hover:bg-indigo-100 dark:text-indigo-500 dark:hover:bg-gray-600">
                        <iconify-icon icon="mdi:send" width="24" height="24" rotate="45deg"></iconify-icon>
                  </button>
            </div>
      </div>
</form>
