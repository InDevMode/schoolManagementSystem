@extends('layouts.app')
@section('content')
    <div class="m-5">
        <main>
            <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                <div class="mx-auto max-w-242.5">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="uppercase font-bold text-black dark:text-bodydark">
                            Créer un message
                        </h2>
                        <nav>
                            <ol class="flex items-center gap-2">
                                <li>
                                    <span class="font-medium text-violet-600"><iconify-icon icon="mdi:bell-outline"
                                            width="24" height="24"></iconify-icon>
                                    </span>
                                </li>
                                <li>
                                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                                        href="{{ url('admin/communicate/noticeboard/list') }}"> Discussions</a>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    @include('message')
                    <div class="flex flex-col gap-9">
                        <!-- Contact Form -->
                        <div
                            class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                            <form action="{{ url('admin/communicate/noticeboard/add') }}" method="post"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="p-6.5">
                                    <div class="w-full mb-5">
                                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                            Titre <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="title" name="title" value="{{ old('title') }}" required type="text"
                                            placeholder="Entrez un titre"
                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                                    </div>

                                    <div class="w-full mb-5">
                                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                            Date d'affichage <span class="text-meta-1">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="notice_date" name="notice_date" value="{{ old('notice_date') }}"
                                                required data-class="flatpickr-right"
                                                placeholder="Entrez une date d'affichage'"
                                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                                            <div
                                                class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                                <iconify-icon icon="lucide:calendar"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full mb-5">
                                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                            Date de publication <span class="text-meta-1">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="publish_date" name="publish_date" value="{{ old('publish_date') }}"
                                                required data-class="flatpickr-right"
                                                placeholder="Entrez une date de publication'"
                                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                                            <div
                                                class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                                <iconify-icon icon="lucide:calendar"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full mb-5">
                                        <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">Envoyer à : <span
                                                class="text-meta-1">*</span></h3>
                                        <ul
                                            class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <li
                                                class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                                                <div class="flex items-center ps-3">
                                                    <input id="message_to_teachers" type="checkbox" value="2"
                                                        name="message_to[]"
                                                        class="w-4 h-4 text-violet-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-violet-500 dark:focus:ring-violet-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                    <label for="message_to_teachers"
                                                        class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Professeurs</label>
                                                </div>
                                            </li>
                                            <li
                                                class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                                                <div class="flex items-center ps-3">
                                                    <input id="message_to_students" type="checkbox" value="3"
                                                        name="message_to[]"
                                                        class="w-4 h-4 text-violet-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-violet-500 dark:focus:ring-violet-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                    <label for="message_to_students"
                                                        class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Apprenants</label>
                                                </div>
                                            </li>
                                            <li
                                                class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                                                <div class="flex items-center ps-3">
                                                    <input id="message_to_parents" type="checkbox" value="4"
                                                        name="message_to[]"
                                                        class="w-4 h-4 text-violet-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-violet-500 dark:focus:ring-violet-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                    <label for="message_to_parents"
                                                        class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Parents</label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="mb-4.5">
                                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                            Message <span class="text-meta-1">*</span>
                                        </label>
                                        <textarea id="compose-textarea" name="message"
                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 text-gray-200 placeholder-gray-200 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                                            required>{{ old('message') }}</textarea>
                                    </div>
                                    <button type="submit"
                                        class="flex w-full justify-center rounded-lg bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90">
                                        Créer
                                    </button>
                                </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const textarea = document.getElementById("compose-textarea");
        if (textarea) {
            // Summernote nécessite jQuery, donc on déclenche via jQuery depuis JS natif
            window.jQuery(textarea).summernote({
                placeholder: 'Entrez votre message ici...',
                height: 200
            });
        }
    });
</script>
