@extends('layouts.app')
@section('content')
<div class="m-5">
    <main>
        <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
            <div class="mx-auto max-w-242.5">
                <div
                    class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <h2 class="uppercase font-bold text-black dark:text-bodydark">
                        Assignez une classe à un ou des professeur(s)
                    </h2>
                    <nav>
                        <ol class="flex items-center gap-2">
                            <li>
                                <span class="font-medium text-emerald-400"><i class="fa-solid fa-landmark"></i></span>
                            </li>
                            <li>
                                /<a class="font-medium hover:text-emerald-400 transition duration-300"
                                    href="{{ url('admin/assign_class/list') }}"> Liste des classes assignées</a>
                            </li>
                        </ol>
                    </nav>
                </div>
                @include('message')
                <div class="flex flex-col gap-9">
                    <!-- Contact Form -->
                    <div
                        class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark z-10"
                    >
                        <form action="" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="p-6.5">
                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Classe <span class="text-meta-1">*</span>
                                    </label>
                                    <div
                                        x-data="{ isOptionSelected: false }"
                                        class="relative z-20 bg-gray-100 dark:bg-form-input"
                                    >
                                        <select id="class_id" name="class_id" required
                                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-emerald-400 active:border-emerald-400 dark:border-form-strokedark dark:bg-form-input dark:focus:border-emerald-400"
                                                :class="isOptionSelected && 'text-black dark:text-white'"
                                                @change="isOptionSelected = true"
                                        >
                                            <option selected disabled value="" class="text-body">
                                                Choisissez une classe pour assignation
                                            </option>
                                            @foreach($getClass as $class)
                                            <option class="text-body" value="{{ $class->id }}" {{ $getClassTeacher->class_id == $class->id ? 'selected' : '' }}>{{ $class->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                        <span
                                            class="absolute right-4 top-1/2 z-30 -translate-y-1/2"
                                        >
                                                <svg
                                                    class="fill-current"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                  <g opacity="0.8">
                                                    <path
                                                        fill-rule="evenodd"
                                                        clip-rule="evenodd"
                                                        d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z"
                                                        fill=""
                                                    ></path>
                                                  </g>
                                                </svg>
                                            </span>
                                    </div>
                                </div>
                                <div class="mb-4.5">
                                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                        Professeurs <span class="text-meta-1">*</span>
                                    </label>
                                    <div>
                                        <select class="hidden" x-cloak id="teacher_id" name="teacher_id[]" required multiple>
                                            @foreach($getTeacher as $teacher)
                                            @php
                                            $selected = "";
                                            @endphp
                                            @foreach($getAssignClass as $getAssign)
                                            @if($getAssign->teacher_id == $teacher->id)
                                            @php
                                            $selected = "selected";
                                            @endphp
                                            @endif
                                            @endforeach
                                            <option value="{{ $teacher->id }}" {{ $selected }}>
                                                {{ $teacher->name }} {{ $teacher->last_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <div x-data="dropdown()" x-init="loadOptions()" class="flex flex-col items-center">
                                            <input name="values" type="hidden" :value="selectedValues()" />
                                            <div class="relative z-30 inline-block w-full">
                                                <div class="relative flex flex-col items-center">
                                                    <div @click="open" class="w-full">
                                                        <div class="mb-2 flex rounded-lg border border-stroke py-2 pl-3 pr-3 outline-none bg-gray-100 transition focus:border-emerald-400 active:border-emerald-400 dark:border-form-strokedark dark:bg-form-input">
                                                            <div class="flex flex-auto flex-wrap gap-3">
                                                                <template x-for="(option, index) in selected" :key="index">
                                                                    <div class="my-1.5 flex items-center justify-center rounded-lg border-[.5px] border-stroke px-2.5 py-1.5 text-sm font-medium dark:border-strokedark dark:bg-white/30">
                                                                        <div class="max-w-full flex-initial" x-text="option.text"></div>
                                                                        <div class="flex flex-auto flex-row-reverse">
                                                                            <div @click="remove(index)" class="cursor-pointer pl-2 hover:text-danger">
                                                                                <svg class="fill-current" role="button" width="12" height="12" viewBox="0 0 12 12">
                                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.35355 3.35355C9.54882 3.15829 9.54882 2.84171 9.35355 2.64645C9.15829 2.45118 8.84171 2.45118 8.64645 2.64645L6 5.29289L3.35355 2.64645C3.15829 2.45118 2.84171 2.45118 2.64645 2.64645C2.45118 2.84171 2.45118 3.15829 2.64645 3.35355L5.29289 6L2.64645 8.64645C2.45118 8.84171 2.45118 9.15829 2.64645 9.35355C2.84171 9.54882 3.15829 9.54882 3.35355 9.35355L6 6.70711L8.64645 9.35355C8.84171 9.54882 9.15829 9.54882 9.35355 9.35355C9.54882 9.15829 9.54882 8.84171 9.35355 8.64645L6.70711 6L9.35355 3.35355Z"></path>
                                                                                </svg>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <div x-show="selected.length == 0" class="flex-1">
                                                                    <input placeholder="Choisissez un ou des professeur(s) à cette classe" class="h-full w-full appearance-none bg-gray-100 p-1 px-2 outline-none" />
                                                                </div>
                                                            </div>
                                                            <div class="flex w-8 items-center py-1 pl-1 pr-1">
                                                                <button type="button" @click="open" class="h-6 w-6 cursor-pointer outline-none" :class="isOpen() ? 'rotate-180' : ''">
                                                                    ▼
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="w-full px-4">
                                                        <div x-show="isOpen()" class="absolute top-full left-0 z-40 w-full overflow-y-auto rounded bg-white shadow dark:bg-form-input">
                                                            <div class="flex w-full flex-col">
                                                                <template x-for="(option, index) in options" :key="index">
                                                                    <div class="w-full cursor-pointer border-b border-stroke hover:bg-emerald-400/5 dark:border-form-strokedark" @click="select(index)">
                                                                        <div :class="option.selected ? 'border-emerald-400' : ''" class="relative flex w-full items-center border-l-2 border-transparent p-2 pl-2">
                                                                            <div class="mx-2 leading-6" x-text="option.text"></div>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Status <span class="text-meta-1">*</span>
                                    </label>
                                    <div
                                        x-data="{ isOptionSelected: false }"
                                        class="relative z-20 bg-gray-100 dark:bg-form-input"
                                    >
                                        <select id="status" name="status" required
                                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-emerald-400 active:border-emerald-400 dark:border-form-strokedark dark:bg-form-input dark:focus:border-emerald-400"
                                                :class="isOptionSelected && 'text-black dark:text-white'"
                                                @change="isOptionSelected = true"
                                        >
                                            <option selected disabled value="" class="text-body">
                                                Choisissez un statut pour cette assignation
                                            </option>
                                            <option class="text-body" value="1" {{ (old(
                                            'status', $getClassTeacher->status) == '1') ? 'selected' : '' }}>Activée</option>
                                            <option class="text-body" value="0" {{ (old(
                                            'status', $getClassTeacher->status) == '0') ? 'selected' : '' }}>Désactivée</option>
                                        </select>
                                        <span
                                            class="absolute right-4 top-1/2 z-30 -translate-y-1/2"
                                        >
                                        <svg
                                            class="fill-current"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                          <g opacity="0.8">
                                            <path
                                                fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z"
                                                fill=""
                                            ></path>
                                          </g>
                                        </svg>
                                      </span>
                                    </div>
                                </div>
                                <button type="submit"
                                        class="flex w-full justify-center rounded-lg bg-emerald-400 p-3 font-medium text-gray hover:bg-opacity-90"
                                >
                                    Réassignez
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection


<script>
    function dropdown() {
        return {
            options: [],
            selected: [],
            show: false,

            open() {
                this.show = !this.show;
            },
            close() {
                this.show = false;
            },
            isOpen() {
                return this.show;
            },

            select(index) {
                const option = this.options[index];
                if (!option) return;

                this.selected.push(option);
                this.options.splice(index, 1);
            },

            remove(index) {
                const option = this.selected[index];
                if (!option) return;

                this.selected.splice(index, 1);
                this.options.push(option);
            },

            loadOptions() {
                const selectElement = document.getElementById("teacher_id");
                const options = Array.from(selectElement.options);

                this.options = options.map(option => ({
                    value: option.value,
                    text: option.text,
                    selected: option.selected
                }));

                this.selected = this.options.filter(opt => opt.selected);
                this.options = this.options.filter(opt => !opt.selected);
            },

            selectedValues() {
                return this.selected.map(option => option.value);
            },
        };
    }
</script>
