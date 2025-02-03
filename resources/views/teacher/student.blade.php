@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        @include('message')
        <div class="mb-10">
            <div class="flex justify-between pt-2">
                <div class="space-x-2 font-semibold mt-3">
                    <span class="text-violet-500"><i class="fa-solid fa-person-breastfeeding"></i></span>
                    <span><i class="fa-solid fa-chevron-right"></i></span>
                    <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                            href="{{ url('teacher/my_student') }}">Liste de mes élèves</a></span>
                    <span><i class="fa-solid fa-chevron-right"></i></span>
                    <span>Mes Elèves</span>
                </div>
            </div>
            <div class="">
                <div class="mt-4">
                    {{ $getTeacherStudent->links('vendor.pagination.tailwind') }}
                </div>
            </div>
            <form action="" method="get"
                  class="my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
                {{ csrf_field() }}
                <div class="grid grid-cols-6 gap-x-5 gap-y-2">
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
                                class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full">
                            <option value="">Filtrer par statut</option>
                            <option value="1" {{ Request::get(
                            'status') == '1' ? 'selected' : '' }}>Activée</option>
                            <option value="0" {{ Request::get(
                            'status') == '0' ? 'selected' : '' }}>Désactivée</option>
                        </select>
                    </div>
                    <div class="">
                        <input type="text" id="mobile_number" name="mobile_number"
                               value="{{ Request::get('mobile_number') }}"
                               class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block text-sm p-2 w-full"
                               placeholder="Rechercher par numéro de téléphone...">
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
                        <input type="date" id="created_at" name="created_at" value="{{ Request::get('created_at') }}"
                               class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                               placeholder="Rechercher par date de création...">
                    </div>
                    <div class="">
                        <input type="date" id="updated_at" name="updated_at" value="{{ Request::get('updated_at') }}"
                               class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                               placeholder="Rechercher par date de modification...">
                    </div>
                    <button type="submit"
                            class="flex justify-between text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                        Rechercher
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900">
                            <i class="fa-solid fa-search text-white"></i>
                        </span>
                    </button>
                    <a href="{{ url('teacher/my_student') }}"
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
                                Nom et prénoms de l'élève
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
                            <div class="flex items-center">
                                Date de modification
                                <a href="#">
                                    <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                                </a>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($getTeacherStudent as $index => $teacherStudent)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="w-4 p-4">
                            <div class="flex items-center">
                                <input id="checkbox-table-search-1" type="checkbox"
                                       class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 focus:outline-none checked:bg-violet-600">
                                <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                            </div>
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{$teacherStudent -> admission_number }}
                        </th>
                        <td class="px-6 py-4">
                            <span>{{ $teacherStudent -> name }}</span>
                            <span>{{ $teacherStudent -> last_name }}</span>
                        </td>
                        <td class="px-6 py-4">
                        <span
                            class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-gray-400">{{ $teacherStudent -> email }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($teacherStudent->status == 0)
                            <div class="flex items-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
                                Inactif
                            </div>
                            @elseif($teacherStudent->status == 1)
                            <div class="flex items-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div>
                                Actif
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($teacherStudent->date_of_birth)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($teacherStudent->gender == 'male')
                            <div
                                class="bg-pink-100 text-pink-800 font-medium text-xs me-2 px-2.5 py-0.5 rounded-full border border-pink-400">
                                Masculin
                            </div>
                            @elseif($teacherStudent->gender == 'female')
                            <div
                                class="bg-violet-100 text-violet-800 font-medium text-xs me-2 px-2.5 py-0.5 rounded-full border border-violet-400">
                                Féminin
                            </div>
                            @elseif($teacherStudent->gender == 'other')
                            <div
                                class="bg-slate-100 text-slate-800 font-medium text-xs me-2 px-2.5 py-0.5 rounded-full border border-slate-400">
                                Autre
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ $teacherStudent->class_name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($teacherStudent->created_at)->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($teacherStudent->updated_at)->format('d/m/Y H:i:s') }}
                        </td>
                    </tr>
                    @endforeach
                    @if($getTeacherStudent->isEmpty())
                    <tr>
                        <td colspan="10" class="p-6 text-center text-gray-500">
                            Aucun élève assigné à ce professeur trouvé.
                        </td>
                    </tr>
                    @endif
                    </tbody>
                </table>
                <div class="text-center bg-white py-2">
                    <div class="flex justify-between items-center mt-4">
                    <span
                        class="text-violet-500 font-bold text-md ps-5 uppercase">Total : {{ $getTeacherStudent->total() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

