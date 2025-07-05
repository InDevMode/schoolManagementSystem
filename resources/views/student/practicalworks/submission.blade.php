@extends('layouts.app')
@section('content')
    <div class="m-2">
        <div class="container mx-auto px-4 py-8 max-w-6xl">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                        <iconify-icon icon="mdi:notebook-edit" class="text-violet-600 mr-2" width="28"
                            height="28"></iconify-icon>
                        Soumettre un travail de maison
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Remplissez les détails pour soumettre votre
                        travail de maison</p>
                </div>

                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ url('student/dashboard') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-violet-600 dark:text-gray-400 dark:hover:text-white">
                                <iconify-icon icon="mdi:home-outline" class="mr-2" width="16"
                                    height="16"></iconify-icon>
                                Tableau de bord
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                    height="16"></iconify-icon>
                                <a href="{{ url('student/practicalworks/list ') }}"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-violet-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Travaux
                                    de maison</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                    height="16"></iconify-icon>
                                <span
                                    class="ml-1 text-sm font-medium text-violet-600 md:ml-2 dark:text-gray-400">Nouveau</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Main Form Section -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-800 transition-colors duration-300">
                <div class="p-6 md:p-8">
                    <form action="{{ url('student/practicalworks/submit') }}" method="post"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <!-- File Upload -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document</label>

                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200">

                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <iconify-icon id="file-icon" icon="mdi:cloud-upload-outline"
                                            class="text-gray-500 dark:text-gray-400 mb-2" width="32"
                                            height="32"></iconify-icon>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Cliquez pour télécharger</span> ou glissez-déposez</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PDF, Word (DOC, DOCX), Excel
                                            (XLS, XLSX), PowerPoint (PPT, PPTX), Images (JPG, PNG, GIF) - MAX. 10MB</p>
                                        <span id="file-name"
                                            class="mt-2 text-sm text-violet-600 dark:text-violet-400 font-medium hidden"></span>
                                    </div>

                                    <input id="dropzone-file" type="file" name="document_file" class="hidden" />
                                </label>
                            </div>
                            <!-- Message d'erreur pour la taille du fichier -->
                            <div id="file-size-error" class="mt-2 text-sm text-red-600 dark:text-red-400 hidden">
                                Le fichier est trop volumineux. La taille maximale autorisée est de 10 MB.
                            </div>
                            <!-- Message d'erreur pour le type de fichier -->
                            <div id="file-type-error" class="mt-2 text-sm text-red-600 dark:text-red-400 hidden">
                                Type de fichier non autorisé. Veuillez sélectionner un fichier PDF, Word, Excel, PowerPoint
                                ou une image.
                            </div>

                            <!-- 📄 Aperçu PDF -->
                            <iframe id="preview-pdf" class="mt-4 w-full h-64 border rounded hidden"
                                frameborder="0"></iframe>

                            <!-- 🖼️ Aperçu Image -->
                            <img id="preview-image"
                                class="mt-4 w-full h-auto max-h-64 object-contain rounded border hidden" />

                            <!-- 📑 Aperçu Office -->
                            <iframe id="preview-office" class="mt-4 w-full h-64 border rounded hidden"
                                frameborder="0"></iframe>
                        </div>


                        <div class="mb-4.5">
                            <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                Description <span class="text-meta-1">*</span>
                            </label>
                            <textarea id="compose-textarea" name="description"
                                class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 text-gray-200 placeholder-gray-200 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                                required>{{ old('description') }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" id="submit-button"
                                class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50 transition-all duration-300">
                                <iconify-icon icon="mdi:content-save-check-outline" class="mr-2" width="20"
                                    height="20"></iconify-icon>
                                Soumettez votre travail de maison
                            </button>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    @endsection

    <script>

        // Initialize Summernote
        document.addEventListener("DOMContentLoaded", function() {
            const textarea = document.getElementById("compose-textarea");
            if (textarea) {
                window.jQuery(textarea).summernote({
                    placeholder: 'Entrez la description du travail ici...',
                    height: 200,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            }

            // Gestion du téléchargement de fichier
            const dropzoneFile = document.getElementById('dropzone-file');
            const fileNameSpan = document.getElementById('file-name');
            const previewPDF = document.getElementById('preview-pdf');
            const previewImage = document.getElementById('preview-image');
            const previewOffice = document.getElementById('preview-office');
            const fileIcon = document.getElementById('file-icon');
            const errorSize = document.getElementById('file-size-error');
            const errorType = document.getElementById('file-type-error');
            const submitButton = document.getElementById('submit-button');
            const MAX_SIZE = 10 * 1024 * 1024;

            const types = {
                'application/pdf': {
                    icon: 'mdi:file-pdf-box',
                    view: 'pdf'
                },
                'image/jpeg': {
                    icon: 'mdi:file-image',
                    view: 'image'
                },
                'image/png': {
                    icon: 'mdi:file-image',
                    view: 'image'
                },
                'image/gif': {
                    icon: 'mdi:file-image',
                    view: 'image'
                },
                'application/msword': {
                    icon: 'mdi:file-word-box',
                    view: 'none'
                },
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document': {
                    icon: 'mdi:file-word-box',
                    view: 'none'
                },
                'application/vnd.ms-excel': {
                    icon: 'mdi:file-excel-box',
                    view: 'none'
                },
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': {
                    icon: 'mdi:file-excel-box',
                    view: 'none'
                },
                'application/vnd.ms-powerpoint': {
                    icon: 'mdi:file-powerpoint-box',
                    view: 'none'
                },
                'application/vnd.openxmlformats-officedocument.presentationml.presentation': {
                    icon: 'mdi:file-powerpoint-box',
                    view: 'none'
                },
            };

            dropzoneFile.addEventListener('change', function(e) {
                const file = e.target.files[0];

                // Reset affichage
                errorSize.classList.add('hidden');
                errorType.classList.add('hidden');
                previewPDF.classList.add('hidden');
                previewImage.classList.add('hidden');
                previewOffice.classList.add('hidden');
                fileNameSpan.classList.add('hidden');
                fileIcon.setAttribute('icon', 'mdi:cloud-upload-outline');
                submitButton.disabled = false;

                if (!file) return;

                if (file.size > MAX_SIZE) {
                    errorSize.classList.remove('hidden');
                    submitButton.disabled = true;
                    return;
                }

                const typeInfo = types[file.type];
                if (!typeInfo) {
                    errorType.classList.remove('hidden');
                    submitButton.disabled = true;
                    return;
                }

                fileNameSpan.textContent = file.name;
                fileNameSpan.classList.remove('hidden');
                fileIcon.setAttribute('icon', typeInfo.icon);

                const fileURL = URL.createObjectURL(file);
                switch (typeInfo.view) {
                    case 'pdf':
                        previewPDF.src = fileURL;
                        previewPDF.classList.remove('hidden');
                        break;
                    case 'image':
                        previewImage.src = fileURL;
                        previewImage.classList.remove('hidden');
                        break;
                    case 'none':
                        // Option : afficher résumé ou fiche (nom, type, poids)
                        break;
                }
            });

        });


    </script>
