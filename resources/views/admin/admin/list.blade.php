@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        @include('message')
        <div class="flex justify-between pt-2">
            <div class="space-x-2 font-semibold">
                <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-user-secret"></i></span>
                <span>/</span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                        href="{{ url('admin/dashboard') }}">Dashboard</a></span>
                <span>/</span>
                <span>Liste des administrateurs</span>
            </div>
            <a href="{{ url('admin/admin/add') }}"
               class="shadow-lg text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-bold uppercase rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full sm:w-fit hover:scale-105">
                Créer un nouveau Administrateur
            </a>
        </div>
        <div class="">
            <div class="mt-4">
                {{ $getAdmin->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        <form action="" method="get"
              class="flex justify-between my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
            {{ csrf_field() }}
            <div class="">
                <input type="text" id="name" name="name" value="{{ Request::get('name') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                       placeholder="Rechercher par nom...">
            </div>
            <div class="">
                <input type="text" id="last_name" name="last_name" value="{{ Request::get('last_name') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                       placeholder="Rechercher par prénom...">
            </div>
            <div class="">
                <input type="email" id="email" name="email" value="{{ Request::get('email') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                       placeholder="Rechercher par email...">
            </div>
            <div>
                <select id="status" name="status"
                        class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2">
                    <option value="">Filtrer par statut</option>
                    <option value="1" {{ Request::get(
                    'status') == '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ Request::get(
                    'status') == '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="">
                <input type="date" id="created_at" name="created_at" value="{{ Request::get('created_at') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                       placeholder="Rechercher par date de création...">
            </div>
            <div class="">
                <input type="date" id="updated_at" name="updated_at" value="{{ Request::get('updated_at') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                       placeholder="Rechercher par date de modification...">
            </div>
            <div class="flex">
                <button type="submit"
                        class="flex justify-between text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-fit hover:scale-105">
                    Rechercher
                    <span
                        class="inline-flex items-center px-3 text-sm text-gray-900">
                            <i class="fa-solid fa-search text-white"></i>
                        </span>
                </button>
                <a href="{{ url('admin/admin/list') }}"
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
                            ID
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
                            Prénoms
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Email
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
                @foreach($getAdmin as $index => $user)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="checkbox-table-search-1" type="checkbox"
                                   class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 focus:outline-none checked:bg-violet-600">
                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        {{ $index + 1 }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $user -> name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $user -> last_name }}
                    </td>
                    <td class="px-6 py-4">
                        <span
                            class="bg-slate-100 text-slate-900 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-gray-400">{{ $user -> email }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->status == 0)
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
                            Inactif
                        </div>
                        @elseif($user->status == 1)
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 me-2"></div>
                            Actif
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i:s') }}
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
                                    <a href="{{ url('admin/admin/edit', $user -> id) }}"
                                       class="font-medium flex items-center space-x-5 px-4 py-3 hover:bg-gray-100 text-[12px] text-violet-500"
                                       title="Modifier">
                                        <span><i class="fa-solid fa-pen-to-square"></i></span>
                                        <span>Modifier</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/admin/delete', $user -> id) }}"
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
                @if($getAdmin->isEmpty())
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        Aucun administrateur trouvé.
                    </td>
                </tr>
                @endif
                </tbody>
            </table>
            <div class="text-center bg-white py-2">
                <div class="flex justify-between items-center mt-4">
                    <span
                        class="text-violet-500 font-bold text-md ps-5 uppercase">Total : {{ $getAdmin->total() }}</span>
                </div>
            </div>
        </div>

        <div data-dial-init class="fixed end-6 bottom-6 group">
            <div id="speed-dial-menu-default" class="flex flex-col items-center hidden mb-4 space-y-2">
                <a href="{{ url('admin/admin/add') }}" data-tooltip-target="tooltip-administrates" data-tooltip-placement="left"
                   class="flex justify-center items-center w-full h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 focus:outline-none">
                    <i class="fa-solid fa-user-secret"></i>
                    <span class="sr-only">Créer un administrateur</span>
                </a>
                <div id="tooltip-administrates" role="tooltip"
                     class="absolute z-10 invisible inline-block w-auto px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                    Créer un administrateur
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>
            <button type="button" data-dial-toggle="speed-dial-menu-default" aria-controls="speed-dial-menu-default"
                    aria-expanded="false"
                    class="flex items-center justify-center text-white bg-violet-500 rounded-full w-14 h-14 hover:bg-violet-600 focus:ring-4 focus:ring-violet-300 focus:outline-non">
                <span class="transition-transform group-hover:rotate-45"><i class="fa-solid fa-2x fa-plus"></i></span>
                <span class="sr-only">Open actions menu</span>
            </button>
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


