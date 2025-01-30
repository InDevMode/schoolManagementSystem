@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="space-x-2 font-semibold">
            <span class="text-emerald-500 text-[25px]"><i class="fa-solid fa-book-open-reader"></i></span>
            <span>/</span>
            <span class="hover:underline hover:text-emerald-500 transition-all duration-300"><a
                    href="{{ url('admin/subject/list') }}">Listes des matières</a></span>
            <span>/</span>
            <span>Matière</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
                @include('message')
                <h2 class="bg-emerald-500 font-bold uppercase text-center text-white rounded-t-lg py-3">
                    Modifier cette matière</h2>
                <form action="" method="post" class="p-5">
                    {{ csrf_field() }}
                    <div class="flex mb-5">
                        <input type="text" id="name" name="name" value="{{ $getSubject->name }}"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="nom de la matière..." required>
                    </div>
                    <div class="flex mb-5">
                        <select id="type" name="type"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Définissez un type pour cette matière</option>
                            <option value="theoretical" name="type" {{ $getSubject->type == 'theoretical' ? 'selected' :
                                '' }}>Théorique
                            </option>
                            <option value="practical" name="type" {{ $getSubject->type == 'practical' ? 'selected' : ''
                                }}>Pratique
                            </option>
                        </select>
                    </div>
                    <div class="flex mb-5">
                        <select id="status" name="status"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Définissez un statut pour cette classe</option>
                            <option value="1" name="status" {{ $getSubject->status == 1 ? 'selected' : '' }}>Activée
                            </option>
                            <option value="0" name="status" {{ $getSubject->status == 0 ? 'selected' : ''
                                }}>Désactivée
                            </option>
                        </select>
                    </div>
                    <button type="submit"
                            class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-md text-sm px-5 py-2.5 text-center transition-all duration-700 ease-out w-full">
                        Modifier
                    </button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

