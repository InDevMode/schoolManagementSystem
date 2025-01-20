@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        @include('message')
        <div class="flex justify-between pt-2">
            <div class="space-x-2 font-semibold">
                <span class="text-emerald-500 text-[25px]"><i class="fa-solid fa-user-tie"></i></span>
                <span>/</span>
                <span class="hover:underline hover:text-emerald-500 transition-all duration-300"><a
                        href="{{ url('admin/teacher/list') }}">Listes des professeurs</a></span>
                <span>/</span>
                <span>Professeur</span>
            </div>
        </div>
    </div>
</div>
@endsection

