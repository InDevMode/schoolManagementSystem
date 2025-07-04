@extends('layouts.app')
@section('content')
    <div class="m-5">
        <main>
            <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                <div class="mx-auto max-w-242.5">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="uppercase font-bold text-black dark:text-bodydark">
                            Modifier une notification
                        </h2>
                        <nav>
                            <ol class="flex items-center gap-2">
                                <li>
                                    <span class="font-medium text-emerald-400"><iconify-icon icon="mdi:bell-outline"
                                            width="24" height="24"></iconify-icon>
                                </li>
                                <p>/</p>
                                <li>
                                    <a class="font-medium hover:text-emerald-400 transition duration-300"
                                        href="{{ url('admin/communicate/noticeboard/list') }}"> Notifications</a>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    @include('message')
                    <div class="flex flex-col gap-9">
                        <!-- Contact Form -->
                        <div
                            class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                            <form action="" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="p-6.5">

                                    <div class="w-full mb-5">
                                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                            Titre <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="title" name="title" value="{{ old('title', $getNoticeBoard->title) }}"
                                            required type="text" placeholder="Entrez un titre"
                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                                    </div>

                                    <div class="w-full mb-5">
                                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                            Date d'affichage <span class="text-meta-1">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="notice_date" name="notice_date"
                                                value="{{ old('notice_date', $getNoticeBoard->notice_date) }}" required
                                                data-class="flatpickr-right" placeholder="Entrez une date d'affichage'"
                                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
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
                                            <input id="publish_date" name="publish_date"
                                                value="{{ old('publish_date', $getNoticeBoard->publish_date) }}" required
                                                data-class="flatpickr-right" placeholder="Entrez une date de publication'"
                                                class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                                            <div
                                                class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center">
                                                <iconify-icon icon="lucide:calendar"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $message_to_teacher = $getNoticeBoard->getMessageToSingle($getNoticeBoard->id, 2);
                                        $message_to_student = $getNoticeBoard->getMessageToSingle($getNoticeBoard->id, 3);
                                        $message_to_parent = $getNoticeBoard->getMessageToSingle($getNoticeBoard->id, 4);
                                    @endphp
                                    <div class="w-full mb-5">
                                        <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">Envoyer à : <span
                                                class="text-meta-1">*</span></h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <!-- Teachers -->
                                            <div
                                                class="relative flex items-start bg-gray-50 rounded-lg p-4 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <div class="flex h-5 items-center">
                                                    <input  id="message_to_teachers" type="checkbox" value="2" name="message_to[]" {{ !empty($message_to_teacher) ? 'checked' : '' }}
                                                        value="2" class="checkbox-custom">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="message_to_teachers"
                                                        class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                        <iconify-icon icon="mdi:teacher" class="text-violet-600 mr-2"
                                                            width="18" height="18"></iconify-icon>
                                                        Professeurs
                                                    </label>
                                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Envoyer aux
                                                        enseignants
                                                        concernés</p>
                                                </div>
                                            </div>

                                            <!-- Students -->
                                            <div
                                                class="relative flex items-start bg-gray-50 rounded-lg p-4 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <div class="flex h-5 items-center">
                                                    <input  id="message_to_students" type="checkbox" value="3" name="message_to[]" {{ !empty($message_to_student) ? 'checked' : '' }}
                                                        value="3" class="checkbox-custom">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="message_to_students"
                                                        class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                        <iconify-icon icon="mdi:account-school" class="text-violet-600 mr-2"
                                                            width="18" height="18"></iconify-icon>
                                                        Apprenants
                                                    </label>
                                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Envoyer à tous
                                                        les
                                                        étudiants</p>
                                                </div>
                                            </div>

                                            <!-- Parents -->
                                            <div
                                                class="relative flex items-start bg-gray-50 rounded-lg p-4 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <div class="flex h-5 items-center">
                                                    <input  id="message_to_parents" type="checkbox" value="4" name="message_to[]" {{ !empty($message_to_parent) ? 'checked' : '' }}
                                                        value="4" class="checkbox-custom">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="message_to_parents"
                                                        class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                        <iconify-icon icon="mdi:account-child" class="text-violet-600 mr-2"
                                                            width="18" height="18"></iconify-icon>
                                                        Parents
                                                    </label>
                                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">Envoyer aux
                                                        parents
                                                        concernés</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4.5">
                                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                            Message <span class="text-meta-1">*</span>
                                        </label>
                                        <textarea id="compose-textarea" name="message"
                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 text-gray-200 placeholder-gray-200 px-5 py-2.5 font-normal outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                            required>{{ old('message', $getNoticeBoard->message) }}</textarea>
                                    </div>

                                    <button type="submit"
                                        class="flex w-full justify-center rounded-lg bg-emerald-400 p-3 font-medium text-gray hover:bg-opacity-90">
                                        Modifier
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
