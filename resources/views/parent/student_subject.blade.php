@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        @include('message')
        <div class="mb-10">
            <div class="flex justify-between pt-2">
                <div class="space-x-2 font-semibold mt-3">
                    <span class="text-violet-500"><i class="fa-solid fa-book-open-reader"></i></span>
                    <span><i class="fa-solid fa-chevron-right"></i></span>
                    <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                                href="{{ url('parent/my_student') }}">Liste des mes élèves</a></span>
                    <span><i class="fa-solid fa-chevron-right"></i></span>
                    <span>{{ $getUser->name }} {{ $getUser->last_name }} </span>
                </div>
            </div>
            <div class="">
                <div class="mt-4">
                    {{ $getParentStudentSubject->links('vendor.pagination.tailwind') }}
                </div>
            </div>
            <form action="" method="get"
                  class="my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
                {{ csrf_field() }}
                <div class="grid grid-cols-6 gap-x-5 gap-y-2">
                    <div class="">
                        <input type="text" id="subject_name" name="subject_name" value="{{ Request::get('subject_name') }}"
                               class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                               placeholder="Rechercher par nom de la matière...">
                    </div>
                    <div class="">
                        <input type="text" id="teacher_name" name="teacher_name" value="{{ Request::get('teacher_name') }}"
                               class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                               placeholder="Rechercher par du professeur...">
                    </div>
                    <div>
                        <select id="subject_type" name="subject_type"
                                class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2">
                            <option value="">Filtrer par type de matière</option>
                            <option value="theoretical" {{ Request::get('subject_type') == 'theoretical' ? 'selected' : '' }}>Théorique</option>
                            <option value="practical" {{ Request::get('subject_type') == 'practical' ? 'selected' : '' }}>Pratique</option>
                        </select>
                    </div>

                    <!-- Statut -->
                    <div>
                        <select id="subject_status" name="subject_status"
                                class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2">
                            <option value="">Filtrer par statut</option>
                            <option value="1" {{ Request::get('subject_status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ Request::get('subject_status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="flex justify-between text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                        Rechercher
                        <span
                                class="inline-flex items-center px-3 text-sm text-gray-900">
                            <i class="fa-solid fa-search text-white"></i>
                        </span>
                    </button>
                    <a href="{{ url('parent/my_student/'.$getUser->id.'/subject') }}"
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
                                N°
                                <a href="#">
                                    <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Nom du cours
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
                                Status de la matière
                                <a href="#">
                                    <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Nom & prénoms du Professeur
                                <a href="#">
                                    <span class="w-3 h-3 ms-1.5"><i class="fa-solid fa-filter"></i></span>
                                </a>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($getParentStudentSubject as $index => $parentStudentSubject)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="w-4 p-4">
                            <div class="flex items-center">
                                <input id="checkbox-table-search-1" type="checkbox"
                                       class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 focus:outline-none checked:bg-violet-600">
                                <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span>{{ $index + 1 }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span>{{ $parentStudentSubject -> subject_name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($parentStudentSubject -> subject_type == 'theoretical')
                            <span
                                    class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Théorique</span>
                            @elseif($parentStudentSubject -> subject_type == 'practical')
                            <span
                                    class="bg-violet-100 text-violet-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Pratique</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($parentStudentSubject -> subject_status == 0)
                            <div class="flex items-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
                                Inactif
                            </div>
                            @elseif($parentStudentSubject -> subject_status == 1)
                            <div class="flex items-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div>
                                Actif
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            Professeur
                        </td>

                    </tr>
                    @endforeach
                    @if($getParentStudentSubject->isEmpty())
                    <tr>
                        <td colspan="10" class="p-6 text-center text-gray-500">
                            Aucun cours disponible.
                        </td>
                    </tr>
                    @endif
                    </tbody>
                </table>
                <div class="text-center bg-white py-2">
                    <div class="flex justify-between items-center mt-4">
                    <span
                            class="text-violet-500 font-bold text-md ps-5 uppercase">Total : {{ $getParentStudentSubject->total() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

