@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        @include('message')
        <div class="flex justify-between pt-2">
            <div class="space-x-2 font-semibold">
                <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-user-graduate"></i></span>
                <span>/</span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                        href="{{ url('admin/dashboard') }}">Dashboard</a></span>
                <span>/</span>
                <span>Liste des élèves</span>
            </div>
            <a href="{{ url('admin/student/add') }}"
               class="uppercase shadow-lg text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-bold rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full sm:w-fit hover:scale-105">
                Créer un nouvel élève
            </a>
        </div>
        <div class="">
            <div class="mt-4">
                {{ $getStudent->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        <form action="" method="get"
              class="my-5 shadow p-3 bg-white rounded border border-gray-300"
              id="searchForm">
            {{ csrf_field() }}
            <div class="grid grid-cols-6 gap-x-5 gap-y-2">
                <div class="">
                    <input type="text" id="admission_number" name="admission_number"
                           value="{{ Request::get('admission_number') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="numéro d'admission...">
                </div>
                <div class="">
                    <input type="text" id="last_name" name="last_name" value="{{ Request::get('name') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par le nom...">
                </div>
                <div class="">
                    <input type="text" id="last_name" name="last_name" value="{{ Request::get('last_name') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par le prénom...">
                </div>
                <div class="">
                    <input type="email" id="email" name="email" value="{{ Request::get('email') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par email...">
                </div>
                <div class="">
                    <input type="text" id="mobile_number" name="mobile_number"
                           value="{{ Request::get('mobile_number') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par numéro de téléphone...">
                </div>
                <div>
                    <select id="status" name="status"
                            class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full">
                        <option value="">Filtrer par statut</option>
                        <option value="1" {{ Request::get(
                        'status') == '1' ? 'selected' : '' }}>Activée</option>
                        <option value="0" {{ Request::get(
                        'status') == '0' ? 'selected' : '' }}>Désactivée</option>
                    </select>
                </div>
                <div>
                    <select id="gender" name="gender"
                            class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full">
                        <option value="">Filtrer par genre</option>
                        <option value="male" {{ Request::get(
                        'gender') == 'male' ? 'selected' : '' }}>Masculin</option>
                        <option value="female" {{ Request::get(
                        'gender') == 'female' ? 'selected' : '' }}>Féminin</option>
                        <option value="other" {{ Request::get(
                        'gender') == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div class="">
                    <input type="text" id="class_name" name="class_name" value="{{ Request::get('class_name') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par la classe...">
                </div>
                <div class="">
                    <input type="text" id="height" name="height" value="{{ Request::get('height') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par la taille...">
                </div>
                <div class="">
                    <input type="text" id="weight" name="weight" value="{{ Request::get('weight') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par la poids...">
                </div>
                <div class="">
                    <input type="text" id="religion" name="religion" value="{{ Request::get('religion') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par la religion...">
                </div>
                <div>
                    <select id="blood_group" name="blood_group"
                            class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full">
                        <option value="">Filtrer par groupe sanguin</option>
                        <option {{ Request::get(
                        'blood_group') == 'a+' ? 'selected' : '' }} value="a+">A+</option>
                        <option {{ Request::get(
                        'blood_group') == 'a-' ? 'selected' : '' }} value="a-">A-</option>
                        <option {{ Request::get(
                        'blood_group') == 'b+' ? 'selected' : '' }} value="b+">B+</option>
                        <option {{ Request::get(
                        'blood_group') == 'b-' ? 'selected' : '' }} value="b-">B-</option>
                        <option {{ Request::get(
                        'blood_group') == 'ab+' ? 'selected' : '' }} value="ab+">AB+</option>
                        <option {{ Request::get(
                        'blood_group') == 'ab-' ? 'selected' : '' }} value="ab-">AB-</option>
                        <option {{ Request::get(
                        'blood_group') == 'o+' ? 'selected' : '' }} value="o+">O+</option>
                        <option {{ Request::get(
                        'blood_group') == 'o-' ? 'selected' : '' }} value="o-">O-</option>
                    </select>
                </div>
                <div class="">
                    <input type="date" id="date_of_birth" name="date_of_birth"
                           value="{{ Request::get('date_of_birth') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par date de naissance...">
                </div>
                <div class="">
                    <input type="date" id="created_at" name="created_at" value="{{ Request::get('created_at') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                           placeholder="Rechercher par date de création...">
                </div>
                <button type="submit"
                        class="flex justify-between text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Rechercher
                    <span
                        class="inline-flex items-center px-3 text-sm text-gray-900">
                            <i class="fa-solid fa-search text-white"></i>
                        </span>
                </button>
                <a href="{{ url('admin/student/list') }}"
                   class="text-gray-800 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Réinitialiser les filtres
                </a>

            </div>
        </form>
        <div class="relative overflow-visible shadow-md sm:rounded-lg border border-gray-300 z-10" id="results">
            <table class="w-full text-[10px] text-left rtl:text-right">
                <thead class="text-[10px] text-white uppercase bg-violet-500">
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
                            N° d'admission
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Nom et prénoms
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Nom et Prénoms du Parent
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
                            Date de naissance
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Genre
                            <a href="#">
                                <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                            </a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Classe
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
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($getStudent as $index => $student)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="checkbox-table-search-1" type="checkbox"
                                   class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 focus:outline-none checked:bg-violet-600">
                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        {{$student -> admission_number }}
                    </th>
                    <td class="px-6 py-4">
                        <span>{{ $student -> name }}</span>
                        <span>{{ $student -> last_name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span>{{ $student -> parent_name }}</span>
                        <span>{{ $student -> parent_last_name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span
                            class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-gray-400">{{ $student -> email }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($student->status == 0)
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
                            Désactivé
                        </div>
                        @elseif($student->status == 1)
                        <div class="flex items-center">
                            <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div>
                            Activé
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($student->gender == 'male')
                        <div
                            class="bg-pink-100 text-pink-800 font-medium text-xs me-2 px-2.5 py-0.5 rounded-full border border-pink-400">
                            Masculin
                        </div>
                        @elseif($student->gender == 'female')
                        <div
                            class="bg-violet-100 text-violet-800 font-medium text-xs me-2 px-2.5 py-0.5 rounded-full border border-violet-400">
                            Féminin
                        </div>
                        @elseif($student->gender == 'other')
                        <div
                            class="bg-slate-100 text-slate-800 font-medium text-xs me-2 px-2.5 py-0.5 rounded-full border border-slate-400">
                            Autre
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ $student->class_name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($student->created_at)->format('d/m/Y H:i:s') }}
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
                                    <a href="{{ url('admin/student/edit', $student -> id) }}"
                                       class="font-medium flex items-center space-x-5 px-4 py-3 hover:bg-gray-100 text-[12px] text-violet-500"
                                       title="Modifier">
                                        <span><i class="fa-solid fa-pen-to-square"></i></span>
                                        <span>Modifier</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/student/delete', $student -> id) }}"
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
                @if($getStudent->isEmpty())
                <tr>
                    <td colspan="11" class="p-6 text-center text-gray-500">
                        Aucun élève trouvé.
                    </td>
                </tr>
                @endif
                </tbody>
            </table>
            <div class="text-center bg-white py-2 w-full">
                <div class="flex justify-between items-center mt-4 w-full">
                    <span
                        class="text-violet-500 font-bold text-md ps-5 uppercase">Total : {{ $getStudent->total() }}</span>
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

