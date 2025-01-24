@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg dark:border-gray-700 mt-14">
        @include('message')
        <div class="flex justify-between pt-2">
            <div class="space-x-2 font-semibold">
                <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-landmark"></i></span>
                <span>/</span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                        href="{{ url('admin/dashboard') }}">Dashboard</a></span>
                <span>/</span>
                <span>Liste des classes</span>
            </div>
            <a href="{{ url('admin/class/add') }}"
               class="uppercase shadow font-bold text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full sm:w-fit hover:scale-105">
                Créer une nouvelle classe
            </a>
        </div>
        <div class="font-bold">
            <div class="mt-4">
                {{ $getClass->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        <form action="" method="get"
              class="flex justify-between my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
            {{ csrf_field() }}
            <!-- Nom de la classe -->
            <div>
                <input type="text" id="name" name="name" value="{{ Request::get('name') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                       placeholder="Rechercher par nom de la classe...">
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

            <!-- Créé par -->
            <div>
                <input type="text" id="created_by" name="created_by" value="{{ Request::get('created_by') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                       placeholder="Rechercher par le créateur...">
            </div>

            <!-- Date de création -->
            <div>
                <input type="date" id="created_at" name="created_at" value="{{ Request::get('created_at') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                       placeholder="Rechercher par date de création...">
            </div>

            <!-- Date de modification -->
            <div>
                <input type="date" id="updated_at" name="updated_at" value="{{ Request::get('updated_at') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                       placeholder="Rechercher par date de modification...">
            </div>

            <!-- Boutons -->
            <div class="flex">
                <button type="submit"
                        class="flex justify-between text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-fit hover:scale-105">
                    Rechercher
                    <span class="inline-flex items-center px-3 text-sm text-gray-900">
                <i class="fa-solid fa-search text-white"></i>
            </span>
                </button>
                <a href="{{ url('admin/class/list') }}"
                   class="ms-5 text-gray-800 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-fit hover:scale-105">
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
                            Id
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Nom
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
                @foreach($getClass as $index => $class)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="checkbox-table-search-1" type="checkbox"
                                   class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 focus:outline-none checked:bg-violet-600">
                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $index + 1 }}
                    </th>
                    <td class="px-6 py-4">
                         <span
                             class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-gray-400">{{ $class -> name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($class->status == 0)
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
                            Désactivée
                        </div>
                        @elseif($class->status == 1)
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 me-2"></div>
                            Activée
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ $class -> created_by_name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($class->created_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($class->updated_at)->format('d/m/Y H:i:s') }}
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
                             class="hidden absolute top-full left-0 bg-white rounded-lg shadow w-44 z-50">
                            <ul class="text-sm text-gray-700"
                                aria-labelledby="dropdownMenuIconButton-{{ $index + 1}}">
                                <li>
                                    <a href="{{ url('admin/class/edit', $class -> id) }}"
                                       class="font-medium flex items-center space-x-5 px-4 py-3 hover:bg-gray-100 text-[12px] text-violet-500"
                                       title="Modifier">
                                        <span><i class="fa-solid fa-pen-to-square"></i></span>
                                        <span>Modifier</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/class/delete', $class -> id) }}"
                                       class="font-medium flex items-center space-x-5 px-4 py-3 hover:bg-gray-100 text-red-500 text-[12px]"
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
                @if($getClass->isEmpty())
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        Aucune classe trouvée.
                    </td>
                </tr>
                @endif
                </tbody>
            </table>
            <div class="text-center bg-white py-2">
                <div class="flex justify-between items-center mt-4">
                    <span
                        class="text-violet-500 font-bold text-md ps-5 uppercase">Total : {{ $getClass->total() }}</span>
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
