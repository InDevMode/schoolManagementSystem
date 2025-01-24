@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 mt-14">
        <div class="mb-8 font-semibold space-x-2">
            <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-house-chimney"></i></span>
            <span>/</span>
            <span class="">Dashboard</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">
            <div
                class="max-w-sm p-6 bg-violet-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-violet-500 text-white">
                    <i class="fa-solid fa-2x fa-user-tag"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Utilisateurs</h5>
                </a>
            </div>
            <div
                class="max-w-sm p-6 bg-red-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-red-500 text-white">
                    <i class="fa-solid fa-2x fa-user-secret"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Administrateus</h5>
                </a>
            </div>
            <div
                class="max-w-sm p-6 bg-amber-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-amber-500 text-white">
                    <i class="fa-solid fa-2x fa-user-tie"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Professeurs</h5>
                </a>
            </div>
            <div
                class="max-w-sm p-6 bg-emerald-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-emerald-500 text-white">
                    <i class="fa-solid fa-2x fa-user-graduate"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Elèves</h5>
                </a>
            </div>
            <div
                class="max-w-sm p-6 bg-pink-500 hover:bg-white border border-gray-300 rounded-lg shadow group transition-all duration-700">
                <span class="mb-3 group-hover:text-pink-500 text-white">
                    <i class="fa-solid fa-2x fa-person-breastfeeding"></i>
                </span>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-semibold tracking-tight uppercase group-hover:text-gray-900 text-white">
                        Total des
                        Parents</h5>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

