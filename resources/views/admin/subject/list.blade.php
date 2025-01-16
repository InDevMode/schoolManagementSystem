@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg dark:border-gray-700 mt-14">
        @include('message')
        <div class="flex justify-between pt-2">
            <span class="font-bold uppercase">Liste des matières</span>
            <a href="{{ url('admin/subject/add') }}"
               class="text-white bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full sm:w-fit hover:scale-105">
                Créer une nouvelle matière
            </a>
        </div>
        <div class="">
            <div class="mt-4">
                {{ $getSubject->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        <form action="" method="get" class="flex justify-between my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
            {{ csrf_field() }}
            <!-- Nom de la matière -->
            <div>
                <input type="text" id="name" name="name" value="{{ Request::get('name') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="Nom de la matière...">
            </div>

            <!-- Type -->
            <div>
                <select id="type" name="type"
                        class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500">
                    <option value="">Filtrer par type de matière</option>
                    <option value="theoretical" {{ Request::get(
                    'type') == 'theoretical' ? 'selected' : '' }}>Théorique</option>
                    <option value="practical" {{ Request::get(
                    'type') == 'practical' ? 'selected' : '' }}>Pratique</option>
                </select>
            </div>

            <!-- Statut -->
            <div>
                <select id="status" name="status"
                        class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500">
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
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="Date de création...">
            </div>

            <!-- Date de modification -->
            <div>
                <input type="date" id="updated_at" name="updated_at" value="{{ Request::get('updated_at') }}"
                       class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="Date de modification...">
            </div>

            <!-- Boutons -->
            <div class="flex">
                <button type="submit"
                        class="flex justify-between text-white bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 transition-all duration-500 ease-out w-fit hover:scale-105">
                    Rechercher
                    <span class="inline-flex items-center px-3 text-sm text-gray-900">
                <i class="fa-solid fa-search text-white"></i>
            </span>
                </button>
                <a href="{{ url('admin/subject/list') }}"
                   class="ms-5 text-gray-800 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 transition-all duration-500 ease-out w-fit hover:scale-105">
                    Réinitialiser les filtres
                </a>
            </div>
        </form>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg" id="results">
            <table class="w-full text-sm text-left rtl:text-right text-gray-5000">
                <thead class="text-xs text-white uppercase bg-violet-500">
                <tr>
                    <th scope="col" class="p-4">
                        <div class="flex items-center">
                            <input id="checkbox-all-search" type="checkbox"
                                   class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-violet-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:outline-none checked:bg-violet-600">
                            <label for="checkbox-all-search" class="sr-only">checkbox</label>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Identifiant
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
                            Type de matière
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
                @foreach($getSubject as $index => $subject)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="checkbox-table-search-1" type="checkbox"
                                   class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-violet-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:outline-none checked:bg-violet-600">
                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $index + 1 }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $subject -> name }}
                    </td>
                    <td class="px-6 py-4">
                        @if($subject->type == 'theoretical')
                        <span
                            class="bg-pink-100 text-pink-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Théorique</span>
                        @elseif($subject->type == 'practical')
                        <span
                            class="bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Pratique</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($subject->status == 0)
                        <span
                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Désactivée</span>
                        @elseif($subject->status == 1)
                        <span
                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Activée</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ $subject -> created_by_name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $subject -> created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $subject -> updated_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="flex items-center px-6 py-4">
                        <a href="{{ url('admin/subject/edit', $subject -> id) }}"
                           class="font-medium text-violet-500 me-5" title="Modifier">
                            <span class="w-6 h-6 text-violet-500 text-[22px]"><i class="fa-solid fa-pen-to-square"></i></span>
                        </a>
                        @include('admin.subject.delete')
                        <button data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                                class="font-medium text-violet-500 dark:text-violet-500 hover:underline"
                                title="Supprimer">
                            <span class="w-6 h-6 text-red-500 text-[22px]"><i class="fa-solid fa-trash"></i></span>
                        </button>
                    </td>
                </tr>
                @endforeach
                @if($getSubject->isEmpty())
                <tr>
                    <td colspan="8" class="p-6 text-center text-gray-500">
                        Aucune matière trouvée.
                    </td>
                </tr>
                @endif
                </tbody>
            </table>
            <div class="text-center bg-white p-2">
                <div class="flex justify-between items-center mt-4">
                    <span class="text-violet-500 font-bold text-md ps-3.5 uppercase">Total : {{ $getSubject->total() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('searchForm');
        const resultsContainer = document.getElementById('results');

        form.addEventListener('input', () => {
            const formData = new FormData(form);

            fetch('{{ url("admin/subject/list") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams(formData),
            })
                .then(response => response.text())
                .then(html => {
                    resultsContainer.innerHTML = html;
                })
                .catch(error => console.error('Error:', error));
        });
    });

</script>


