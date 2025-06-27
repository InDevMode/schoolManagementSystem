@extends('layouts.app')
@section('content')
    <div class="m-5">
        <div class="container mx-auto px-4 py-8 max-w-6xl">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                        <iconify-icon icon="mdi:notebook-edit-outline" class="text-green-600 mr-2" width="28"
                            height="28"></iconify-icon>
                        Modifier un travail de maison
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Remplissez les détails pour créer un nouveau
                        travail de maison</p>
                </div>

                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ url('admin/dashboard') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-green-600 dark:text-gray-400 dark:hover:text-white">
                                <iconify-icon icon="mdi:home-outline" class="mr-2" width="16" height="16"></iconify-icon>
                                Tableau de bord
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                    height="16"></iconify-icon>
                                <a href="{{ url('admin/practicalworks/homework/list ') }}"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-green-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Travaux
                                    de maison</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                    height="16"></iconify-icon>
                                <span
                                    class="ml-1 text-sm font-medium text-green-600 md:ml-2 dark:text-gray-400">Modifier</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Main Form Section -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-800 transition-colors duration-300">
                <div class="p-6 md:p-8">
                    <form action="{{ url('admin/practicalworks/homework/add') }}" method="post"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <!-- Class Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Classe <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="class_id" name="class_id" required onchange="loadSubjects(this.value)"
                                    class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500 transition-all duration-200">
                                    <option selected disabled value="">Choisissez une classe pour cet travail de maion
                                    </option>
                                    @foreach($getClass as $class)
                                        <option class="text-body" value="{{ $class->id }}" {{ old('class_id', $getWorks->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <iconify-icon icon="mdi:chevron-down" class="text-gray-400" width="20"
                                        height="20"></iconify-icon>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Matière <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="subject_id" name="subject_id" required
                                    class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500 transition-all duration-200">
                                    <option selected disabled value="">Veuillez choisir une classe d'abord
                                    </option>
                                     @foreach($getSubject as $subject)
                                        <option class="text-body" value="{{ $subject->id }}" {{ old('subject_id', $getWorks->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <iconify-icon icon="mdi:chevron-down" class="text-gray-400" width="20"
                                        height="20"></iconify-icon>
                                </div>
                            </div>
                        </div>

                        <!-- Date Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Date de travail <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="date" id="work_date" name="work_date"
                                        value="{{ old('work_date', $getWorks->work_date) }}" required
                                        class="form-datepicker w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500 transition-all duration-200"
                                        placeholder="Entrez une date de travail">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Date de soumission <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="date" id="submission_date" name="submission_date"
                                        value="{{ old('submission_date', $getWorks->submission_date) }}" required
                                        class="form-datepicker w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500 transition-all duration-200"
                                        placeholder="Entrez une date de soumission">
                                </div>
                            </div>
                        </div>

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
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PDF, DOCX, XLSX, PNG, JPG (MAX.
                                            10MB)</p>
                                        <span id="file-name"
                                            class="mt-2 text-sm text-green-600 dark:text-green-400 font-medium hidden"></span>
                                    </div>

                                    <input id="dropzone-file" type="file" name="document_file" class="hidden" />
                                </label>
                            </div>
                        </div>
                        <div class="text-sm text-gray-900 dark:text-white mb-3">
                            @if (!empty($getWorks->document_file))
                                <a href="{{ url('upload/practicalworks/' . $getWorks->document_file) }}" target="_blank"
                                    class="flex items-center justify-center bg-green-600 text-white px-2.5 py-1.5 rounded-md text-sm font-medium"><iconify-icon
                                        icon="mdi:file-download-outline" width="24" height="24"
                                        class="text-white"></iconify-icon>
                                    Télécharger</a>
                            @endif
                        </div>

                        <div class="mb-4.5">
                            <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                Description <span class="text-meta-1">*</span>
                            </label>
                            <textarea id="compose-textarea" name="description"
                                class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 text-gray-200 placeholder-gray-200 px-5 py-2.5 font-normal outline-none transition focus:border-green-600 active:border-green-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-green-600"
                                required>{{ old('description', $getWorks->description) }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit"
                                class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-all duration-300">
                                <iconify-icon icon="mdi:content-save-check-outline" class="mr-2" width="20"
                                    height="20"></iconify-icon>
                                Modifier le travail de classe
                            </button>
                        </div>
                    </form>
                </div>
            </div>


        </div>
@endsection

    <script>

        // Initialize Summernote
        document.addEventListener("DOMContentLoaded", function () {
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
        });

        // Initialize date pickers (you would replace this with your actual date picker library)
        document.querySelectorAll('.form-datepicker').forEach(input => {
            input.addEventListener('focus', function () {
                this.type = 'date';
            });

            input.addEventListener('blur', function () {
                if (!this.value) {
                    this.type = 'text';
                }
            });
        });

        // File upload handling
        const fileInput = document.getElementById('dropzone-file');
        const fileNameSpan = document.getElementById('file-name');
        const previewImage = document.getElementById('preview-image');
        const previewPdf = document.getElementById('preview-pdf');
        const fileIcon = document.getElementById('file-icon');

        const iconMap = {
            'application/pdf': 'mdi:file-pdf-box',
            'application/msword': 'mdi:file-word-box',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'mdi:file-word-box',
            'application/vnd.ms-excel': 'mdi:file-excel-box',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'mdi:file-excel-box',
            'image/png': 'mdi:file-image',
            'image/jpeg': 'mdi:file-image',
            'default': 'mdi:file-outline'
        };

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];

            if (file) {
                const type = file.type;
                fileNameSpan.textContent = file.name;
                fileNameSpan.classList.remove('hidden');

                // Mise à jour de l'icône
                fileIcon.setAttribute('icon', iconMap[type] || iconMap['default']);

                // Image preview
                if (type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImage.src = e.target.result;
                        previewImage.classList.remove('hidden');
                        previewPdf.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }
                // PDF preview
                else if (type === 'application/pdf') {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewPdf.src = e.target.result;
                        previewPdf.classList.remove('hidden');
                        previewImage.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }
                // Autres types : masquer les aperçus
                else {
                    previewImage.classList.add('hidden');
                    previewPdf.classList.add('hidden');
                }
            } else {
                fileNameSpan.classList.add('hidden');
                fileNameSpan.textContent = '';
                previewImage.classList.add('hidden');
                previewPdf.classList.add('hidden');
                fileIcon.setAttribute('icon', 'mdi:cloud-upload-outline');
            }
        });

        function loadSubjects(classId) {
            const subjectSelect = document.getElementById("subject_id");
            subjectSelect.innerHTML = '<option value="">Chargement...</option>';

            fetch(`{{ url('admin/practicalworks/homework/getSubjectByClassId/${classId}') }}`)
                .then(response => response.json())
                .then(data => {
                    subjectSelect.innerHTML = '<option selected disabled value="">Choisissez une matière</option>';
                    if (data.getSubject && data.getSubject.length > 0) {
                        data.getSubject.forEach(subject => {
                            const option = document.createElement("option");
                            option.value = subject.subject_id;
                            option.text = subject.subject_name;
                            console.log(subject.subject_id, subject.subject_name);
                            subjectSelect.appendChild(option);
                            console.log(subjectSelect);
                        });
                    } else {
                        subjectSelect.innerHTML = '<option value="">Aucune matière trouvée</option>';
                    }
                })
                .catch(error => {
                    console.error("Erreur lors du chargement des matières :", error);
                    subjectSelect.innerHTML = '<option value="">Erreur lors du chargement</option>';
                });
        }


    </script>
