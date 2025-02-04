@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg dark:border-gray-700 mt-14">
        @include('message')
        <div class="flex justify-between pt-2">
            <div class="space-x-2 font-semibold mt-3">
                <span class="text-violet-500"><i class="fa-solid fa-landmark"></i></span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                        href="{{ url('teacher/dashboard') }}">Dashboard</a></span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span>Liste de mes Classes et Matières</span>
            </div>
        </div>
        <div class="">
            <div class="mt-4">
                {{ $getClassSubjectTeacher->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        <form action="" method="get"
              class="flex justify-between my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
            {{ csrf_field() }}

            <div class="grid grid-cols-6 gap-x-5 gap-y-2">
                <!-- Nom de la classe -->
                <div>
                    <input type="text" id="class_name" name="class_name" value="{{ Request::get('class_name') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="Rechercher par le nom de la classe...">
                </div>

                <!-- Nom de la classe -->
                <div>
                    <input type="text" id="subject_name" name="subject_name" value="{{ Request::get('subject_name') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="Rechercher par le nom de la matière...">
                </div>

                <!-- Type -->
                <div>
                    <select id="subject_type" name="subject_type"
                            class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2">
                        <option value="">Filtrer par type de matière</option>
                        <option value="theoretical" {{ Request::get(
                        'type') == 'theoretical' ? 'selected' : '' }}>Théorique</option>
                        <option value="practical" {{ Request::get(
                        'type') == 'practical' ? 'selected' : '' }}>Pratique</option>
                    </select>
                </div>

                <!-- Date de création -->
                <div>
                    <input type="date" id="created_at" name="created_at" value="{{ Request::get('created_at') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="Date de création...">
                </div>

                <!-- Date de modification -->
                <div>
                    <input type="date" id="updated_at" name="updated_at" value="{{ Request::get('updated_at') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="Date de modification...">
                </div>

                <!-- Boutons -->
                <button type="submit"
                        class="flex justify-between text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Rechercher
                    <span class="inline-flex items-center px-3 text-sm text-gray-900">
                        <i class="fa-solid fa-search text-white"></i>
                    </span>
                </button>
                <a href="{{ url('teacher/class_subject') }}"
                   class="text-gray-800 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Réinitialiser les filtres
                </a>
            </div>

        </form>

        <div class="relative overflow-visible shadow-md sm:rounded-lg border border-gray-300 z-10" id="results">
            <table class="w-full text-[12px] text-left rtl:text-right">
                <thead class="text-[12px] text-white uppercase bg-violet-500">
                <tr>
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
                            Nom de la classe
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Nom de la matière
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Heures
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Type de matière
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Date de création
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Date de modification
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Actions
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($getClassSubjectTeacher as $index => $classSubjectTeacher)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        {{ $index + 1 }}
                    </th>
                    <td class="px-6 py-4 font-semibold">
                        {{ $classSubjectTeacher -> class_name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $classSubjectTeacher -> subject_name }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $timetable = \App\Models\ClassTeacherModel::getMyClassTimetable($classSubjectTeacher->class_id, $classSubjectTeacher->subject_id);
                        @endphp
                        @if ($timetable)
                        <span class="bg-gray-200 flex justify-center border border-gray-500 py-2 px-3 rounded">{{ \Carbon\Carbon::parse($timetable->start_time)->format('G\h i\m\i\n') }} à {{ \Carbon\Carbon::parse($timetable->end_time)->format('G\h i\m\i\n') }}</span>
                        @else
                        <p>Aucune heure disponible pour cette matière.</p>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        @if($classSubjectTeacher -> subject_type == 'theoretical')
                        <span
                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Théorique</span>
                        @elseif($classSubjectTeacher -> subject_type == 'practical')
                        <span
                            class="bg-violet-100 text-violet-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Pratique</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($classSubjectTeacher->created_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($classSubjectTeacher->updated_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ url('teacher/class_subject/'. $classSubjectTeacher -> class_id.'/timetable/'. $classSubjectTeacher->subject_id) }}"
                           class="font-medium flex items-center justify-center space-x-2 py-2 text-white hover:bg-violet-600 rounded bg-violet-500 text-sm transition duration-700"
                           title="Voir mon horaire">
                            <span><i class="fa-solid fa-eye"></i></span>
                            <span>Horaire</span>
                        </a>
                    </td>
                </tr>
                @endforeach
                @if($getClassSubjectTeacher->isEmpty())
                <tr>
                    <td colspan="9" class="p-6 text-center text-gray-500">
                        Aucune classe assignée trouvée.
                    </td>
                </tr>
                @endif
                </tbody>
            </table>
            <div class="text-center bg-white p-2">
                <div class="flex justify-between items-center mt-4">
                        <span
                            class="text-violet-500 font-bold text-md ps-3.5 uppercase">Total : {{ $getClassSubjectTeacher->total() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            const dropdownId = button.getAttribute('data-dropdown-toggle');
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('hidden');
        });
    });
</script>

