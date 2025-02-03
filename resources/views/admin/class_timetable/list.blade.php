@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg dark:border-gray-700 mt-14">
        @include('message')
        <div class="flex justify-between pt-2">
            <div class="space-x-2 font-semibold mt-3">
                <span class="text-violet-500"><i class="fa-solid fa-clock"></i></span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                        href="{{ url('admin/dashboard') }}">Dashboard</a></span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span>Liste des Horaires de Cours</span>
            </div>
        </div>
        <div
            class="pt-5 text-red-500 text-sm font-semibold">Veuillez chossir la classe et la matière dont vous souhaiterez définir les horaires</div>
        <form action="" method="get"
              class="my-5 shadow p-3 bg-white rounded border border-gray-300"
              id="searchForm">
            {{ csrf_field() }}
            <div class="grid grid-cols-2 gap-x-5 gap-y-2">
                <div class="flex mb-3">
                    <select id="class_id" name="class_id"
                            class="class_id rounded-full bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                            required>
                        <option disabled selected>Choisissez la classe pour laquelle vous souhaitez définir un horaire
                        </option>
                        @foreach($getClass as $class)
                        <option {{ Request::get(
                        'class_id') == $class->id ? 'selected' : '' }} value="{{ $class->id }}">{{ $class->name
                        }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex mb-3">
                    <select id="subject_id" name="subject_id"
                            class="subject_id rounded-full bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                            required>
                        <option disabled selected>Choisissez la matière pour laquelle vous souhaitez définir un
                            horaire
                        </option>
                        @if(!empty($getSubject))
                        @foreach($getSubject as $subject)
                        <option {{ Request::get(
                        'subject_id') == $subject->subject_id ? 'selected' : '' }} value="{{ $subject->subject_id
                        }}">{{ $subject->subject_name }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <!-- Boutons -->
                <button type="submit"
                        class="flex justify-between text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Rechercher
                    <span class="inline-flex items-center px-3 text-sm text-gray-900">
                        <i class="fa-solid fa-search text-white"></i>
                    </span>
                </button>
                <a href="{{ url('admin/class_timetable/list') }}"
                   class="text-gray-800 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Réinitialiser les filtres
                </a>
            </div>
        </form>

        <div class="mb-5 text-red-500 text-sm font-semibold">Définissez des horaires de cours pour cette classe et cette matière</div>

        @if(!empty(Request::get('class_id') && !empty(Request::get('subject_id'))))
        <form action="{{ url('admin/class_timetable/add') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="subject_id" value="{{ Request::get('subject_id') }}">
            <input type="hidden" name="class_id" value="{{ Request::get('class_id') }}">
            <div class="relative overflow-visible shadow-md sm:rounded-lg border border-gray-300 z-10" id="results">
                <table class="w-full text-[12px] text-left rtl:text-right">
                    <thead class="text-[12px] text-white uppercase bg-violet-500">
                    <tr>
                        <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkbox-all-search" type="checkbox"
                                       class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 focus:outline-none checked:bg-violet-600">
                                <label for="checkbox-all-search" class="sr-only">checkbox</label>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                N°
                                <a href="#">
                                    <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Jour de semaine
                                <a href="#">
                                    <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Heure de début
                                <a href="#">
                                    <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Heure de fin
                                <a href="#">
                                    <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Numéro de salle
                                <a href="#">
                                    <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                                </a>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                    $i = 1;
                    @endphp
                    @foreach($week as $index => $weekData)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="w-4 p-4">
                            <div class="flex items-center">
                                <input id="checkbox-table-search-{{ $index }}" type="checkbox"
                                       class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 focus:outline-none checked:bg-violet-600">
                                <label for="checkbox-table-search-{{ $index }}" class="sr-only">checkbox</label>
                            </div>
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $index + 1 }}
                        </th>
                        <td class="px-6 py-4">
                            <input type="hidden" name="timetable[{{ $index }}][week_id]"
                                   value="{{ $weekData['week_id'] }}">
                            <span
                                class="block w-[100px] text-center bg-violet-100 text-violet-800 text-xs font-medium me-2 px-2.5 py-1 rounded">{{ $weekData['week_name'] }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <input type="time" id="timetable[{{ $index }}][start_time]"
                                   name="timetable[{{ $index }}][start_time]"
                                   value="{{ old('timetable.' . $index . '.start_time', $weekData['start_time']) }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3">
                        </td>
                        <td class="px-6 py-4">
                            <input type="time" id="timetable[{{ $index }}][end_time]"
                                   name="timetable[{{ $index }}][end_time]"
                                   value="{{ old('timetable.' . $index . '.end_time', $weekData['end_time']) }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3">
                        </td>
                        <td class="px-6 py-4">
                            <input type="text" id="timetable[{{ $index }}][room_number]"
                                   name="timetable[{{ $index }}][room_number]"
                                   value="{{ old('timetable.' . $index . '.room_number', $weekData['room_number']) }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3">
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
        document.querySelectorAll(".class_id").forEach(function (element) {
            element.addEventListener("change", function () {
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
                        let subjectDropdown = document.getElementById("subject_id");
                        subjectDropdown.innerHTML = "";

                        let defaultOption = document.createElement("option");
                        defaultOption.disabled = true;
                        defaultOption.selected = true;
                        defaultOption.textContent = "Choisissez la matière pour laquelle vous souhaitez définir un horaire";
                        subjectDropdown.appendChild(defaultOption);

                        if (data.subjects && data.subjects.length > 0) {
                            data.subjects.forEach(subject => {
                                let option = document.createElement("option");
                                option.value = subject.id;
                                option.textContent = subject.name;
                                subjectDropdown.appendChild(option);
                            });
                        } else {
                            let noDataOption = document.createElement("option");
                            noDataOption.disabled = true;
                            noDataOption.textContent = "Aucune matière disponible pour cette classe";
                            subjectDropdown.appendChild(noDataOption);
                        }
                    })
                    .catch(error => {
                        console.error("Erreur javascript :", error);
                    });
            });
        });
    });
</script>
