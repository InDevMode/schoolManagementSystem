@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Liste des horaires de cours
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-clock"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('admin/dashboard') }}"> Dashboard</a>
                </li>
            </ol>
        </nav>
    </div>
    <div
        class="pt-5 text-red-600 dark:text-red-400 text-sm font-semibold">Veuillez chossir la classe et la matière dont vous souhaiterez
        définir les horaires
    </div>
    <form action="" method="get"
          class="my-5 shadow p-3 bg-white rounded border border-gray-300 dark:border-strokedark dark:bg-boxdark"
          id="searchForm">
        {{ csrf_field() }}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 items-center">
            <div class="w-full">
                <div
                    x-data="{ isOptionSelected: false }"
                    class="relative z-20 bg-gray-100 dark:bg-form-input"
                >
                    <select id="class_id" name="class_id"
                            class="rclass_id elative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
                            :class="isOptionSelected && 'text-black dark:text-white'"
                            @change="isOptionSelected = true"
                    >
                        <option disabled selected value="" class="text-body">
                            Choisissez une classe ...
                        </option>
                        @foreach($getClass as $class)
                        <option {{ Request::get(
                        'class_id') == $class->id ? 'selected' : '' }} value="{{ $class->id }}">{{ $class->name
                        }}</option>
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
            <div class="w-full">
                <div
                    x-data="{ isOptionSelected: false }"
                    class="relative z-20 bg-gray-100 dark:bg-form-input"
                >
                    <select id="subject_id" name="subject_id" required
                            class="subject_id relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
                            :class="isOptionSelected && 'text-black dark:text-white'"
                            @change="isOptionSelected = true"
                    >
                        <option disabled selected value="" class="text-body">
                            Choisissez une matière ...
                        </option>
                        @if(!empty($getSubject))
                        @foreach($getSubject as $subject)
                        <option {{ Request::get(
                        'subject_id') == $subject->subject_id ? 'selected' : '' }} value="{{ $subject->subject_id
                        }}">{{ $subject->subject_name }}</option>
                        @endforeach
                        @endif
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
            <!-- Boutons -->
            <div class="w-full">
                <button
                    class="flex w-full justify-between items-center rounded-lg bg-violet-600 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90"
                >
                    Rechercher
                    <span class="inline-flex items-center text-sm text-gray-900">
                                    <i class="fa-solid fa-search text-white"></i>
                                </span>
                </button>
            </div>
            <div class="w-full">
                <a href="{{ url('admin/class_timetable/list') }}"
                   class="flex w-full justify-center rounded-lg bg-gray-500 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90"
                >
                    Réïnitialisez
                </a>
            </div>
        </div>
    </form>

    <div class="mb-5 text-red-600 dark:text-red-400 text-sm font-semibold">Définissez des horaires de cours pour cette classe et cette
        matière
    </div>

    @if(!empty(Request::get('class_id') && !empty(Request::get('subject_id'))))
    <form action="{{ url('admin/class_timetable/add') }}" method="post" class="rounded-lg border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
        {{ csrf_field() }}
        <input type="hidden" name="subject_id" value="{{ Request::get('subject_id') }}">
        <input type="hidden" name="class_id" value="{{ Request::get('class_id') }}">
        <div class="relative overflow rounded-lg z-10">
            <table class="w-full text-sm text-left rtl:text-right text-white dark:text-white">
                <thead
                    class="rounded-sm bg-violet-600 uppercase text-white dark:bg-meta-4"
                >
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Jour
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Heure de début
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Heure de fin
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Salle
                    </th>
                </tr>
                </thead>
                <tbody>
                @php
                $i = 1;
                @endphp
                @foreach($week as $index => $weekData)
                <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition duration-300 border-b dark:border-gray-600 hover:border-violet-400 dark:text-gray-200 text-gray-500">
                    <td class="px-6 py-3">
                        <input type="hidden" name="timetable[{{ $index }}][week_id]"
                               value="{{ $weekData['week_id'] }}">
                        <span
                            class="block w-[100px] rounded-lg border-[1.5px] border-stroke bg-violet-100 px-5 py-2.5 font-normal text-violet-600 outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">{{ $weekData['week_name'] }}</span>
                    </td>
                    <td class="px-6 py-3">
                        <input type="time" id="timetable[{{ $index }}][start_time]"
                               name="timetable[{{ $index }}][start_time]"
                               value="{{ old('timetable.' . $index . '.start_time', $weekData['start_time']) }}"
                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                    </td>
                    <td class="px-6 py-3">
                        <input type="time" id="timetable[{{ $index }}][end_time]"
                               name="timetable[{{ $index }}][end_time]"
                               value="{{ old('timetable.' . $index . '.end_time', $weekData['end_time']) }}"
                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                    </td>
                    <td class="px-6 py-3">
                        <input type="text" id="timetable[{{ $index }}][room_number]"
                               name="timetable[{{ $index }}][room_number]"
                               value="{{ old('timetable.' . $index . '.room_number', $weekData['room_number']) }}"
                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                    </td>
                </tr>
                @php
                $i++
                @endphp
                @endforeach
                @if($getWeek->isEmpty())
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        Aucun horaire de cours trouvé.
                    </td>
                </tr>
                @endif
                </tbody>
            </table>
            <div class="flex ms-4 my-3">
                <button type="submit"
                        class="text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center transition-all duration-700 ease-out w-fit">
                    Ajouter
                </button>
            </div>
        </div>
    </form>

    @endif
</div>
</div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const classSelect = document.getElementById("class_id");
        const subjectSelect = document.getElementById("subject_id");

        classSelect.addEventListener("change", function () {
            const class_id = this.value;

            fetch("/admin/class_timetable/subject", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    "_token": "{{ csrf_token() }}",
                    class_id: class_id
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur serveur : " + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    subjectSelect.innerHTML = "";

                    let defaultOption = document.createElement("option");
                    defaultOption.disabled = true;
                    defaultOption.selected = true;
                    defaultOption.textContent = "Choisissez une matière...";
                    subjectSelect.appendChild(defaultOption);

                    if (data.subjects && data.subjects.length > 0) {
                        data.subjects.forEach(subject => {
                            let option = document.createElement("option");
                            option.value = subject.id;
                            option.textContent = subject.name;
                            subjectSelect.appendChild(option);
                        });
                    } else {
                        let noDataOption = document.createElement("option");
                        noDataOption.disabled = true;
                        noDataOption.textContent = "Aucune matière disponible pour cette classe";
                        subjectSelect.appendChild(noDataOption);
                    }
                })
                .catch(error => {
                    console.error("Erreur javascript :", error);
                });
        });

    });
</script>

