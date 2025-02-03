@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg dark:border-gray-700 mt-14">
        @include('message')
        <div class="flex justify-between mt-3">
            <div class="space-x-2 font-semibold">
                <span class="text-violet-500"><i class="fa-solid fa-arrows-rotate"></i></span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                        href="{{ url('admin/dashboard') }}">Dashboard</a></span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span>Liste des assignations</span>
            </div>
            <a href="{{ url('admin/assign_class/add') }}"
               class="uppercase text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-bold rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full sm:w-fit hover:scale-105">
                Assignez une nouvelle classe
            </a>
        </div>
        <div class="">
            <div class="mt-4">
                {{ $getClassTeacher->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        <form action="" method="get"
              class="flex justify-between my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
            {{ csrf_field() }}

            <div class="grid grid-cols-7 gap-x-5 gap-y-2">
                <!-- Nom de la classe -->
                <div>
                    <input type="text" id="class_name" name="class_name" value="{{ Request::get('class_name') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="Rechercher par le nom de la classe...">
                </div>

                <!-- Nom du professeur -->
                <div>
                    <input type="text" id="teacher_name" name="teacher_name" value="{{ Request::get('teacher_name') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="Rechercher par le nom du professeur...">
                </div>

                <!-- Statut -->
                <div>
                    <select id="status" name="status"
                            class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2">
                        <option value="">Filtrer par statut</option>
                        <option value="1" {{ Request::get(
                        'status') == '1' ? 'selected' : '' }}>Activée</option>
                        <option value="0" {{ Request::get(
                        'status') == '0' ? 'selected' : '' }}>Désactivée</option>
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
                <a href="{{ url('admin/assign_class/list') }}"
                   class="text-gray-800 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Réinitialiser les filtres
                </a>
            </div>

        </form>

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
                            Nom de la classe
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Nom & Prénoms du professeur
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Status
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Crée par
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
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($getClassTeacher as $index => $classTeacher)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input name="ids[]" value="{{ $classTeacher->id }}" type="checkbox"
                                   class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 focus:outline-none checked:bg-violet-600">
                            <label for="checkbox-table-search-{{ $index }}" class="sr-only">checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        {{ $index + 1 }}
                    </th>
                    <td class="px-6 py-4">
                        <div class="block w-[100px] text-center text-xs font-semibold me-2 px-2.5 py-1 rounded
                            {{ ($classTeacher->status === 1) ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                            {{ $classTeacher -> class_name }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        {{ $classTeacher -> teacher_name }} {{ $classTeacher -> teacher_last_name }}
                    </td>
                    <td class="px-6 py-4">
                        @if($classTeacher->status == 0)
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
                            Désactivée
                        </div>
                        @elseif($classTeacher->status == 1)
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div>
                            Activée
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ $classTeacher -> created_by_name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($classTeacher->created_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($classTeacher->updated_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="flex items-center px-6 py-4 relative">
                        <button id="dropdownMenuIconButton-{{ $index + 1}}"
                                data-dropdown-toggle="dropdownDots-{{ $index + 1}}"
                                class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-50"
                                type="button">
                            <i class="fa-solid fa-ellipsis"></i>
                        </button>
                        <!-- Dropdown menu -->
                        <div id="dropdownDots-{{ $index + 1}}"
                             class="hidden absolute top-full left-0 right-10 bg-white rounded-lg shadow w-64 z-50">
                            <ul class="text-sm text-gray-700"
                                aria-labelledby="dropdownMenuIconButton-{{ $index + 1}}">
                                <li>
                                    <a href="{{ url('admin/assign_class/edit_single', $classTeacher -> id) }}"
                                       class="font-medium flex items-center space-x-2 px-4 py-3 hover:bg-gray-100 text-[12px] text-violet-500"
                                       title="Modifier">
                                        <span><i class="fa-solid fa-pen"></i></span>
                                        <span>Modifier une assignation</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/assign_class/edit', $classTeacher -> id) }}"
                                       class="font-medium flex items-center space-x-2 px-4 py-3 hover:bg-gray-100 text-[12px] text-violet-500"
                                       title="Modifier">
                                        <span><i class="fa-solid fa-pen-to-square"></i></span>
                                        <span>Modifier plusieurs assignations</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/assign_class/delete', $classTeacher -> id) }}"
                                       class="font-medium flex items-center space-x-2 px-4 py-3 hover:bg-gray-100 text-red-500 text-[12px]"
                                       title="Supprimer">
                                        <span><i class="fa-solid fa-trash"></i></span>
                                        <span>Supprimer</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($getClassTeacher->isEmpty())
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
                            class="text-violet-500 font-bold text-md ps-3.5 uppercase">Total : {{ $getClassTeacher->total() }}</span>
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

