@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="space-x-2 font-semibold">
            <span class="text-emerald-500 text-[25px]"><i class="fa-solid fa-arrows-rotate"></i></span>
            <span>/</span>
            <span class="hover:underline hover:text-emerald-500 transition-all duration-300"><a
                    href="{{ url('admin/assign_subject/list') }}">Listes des assignations</a></span>
            <span>/</span>
            <span>Assignation</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
                @include('message')
                <h2 class="bg-emerald-500 font-bold uppercase text-center text-white rounded-t-lg py-3 mb-5">
                    Modifier cette assignation</h2>
                <form action="" method="post" class="p-5">
                    {{ csrf_field() }}
                    <div class="flex mb-5">
                        <select id="class_id" name="class_id"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Choisissez la classe pour cette assignation</option>
                            @foreach($getClass as $class)
                            <option {{ ($getClassSubject->class_id == $class->id) ? 'selected' : '' }}
                                value="{{ $class->id
                                }}">{{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex mb-5">
                        <select id="subject_id" name="subject_id"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Choisissez la matière a assignée</option>
                            @foreach($getSubject as $subject)
                            <option {{ ($getClassSubject->subject_id == $subject->id) ? 'selected' : '' }}
                                value="{{ $subject->id
                                }}">{{ $subject->name }}
                                @endforeach
                        </select>
                    </div>
                    <div class="flex mb-5">
                        <select id="status" name="status"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Définissez un status pour cette assignation</option>
                            <option value="1" name="status" {{ ($getClassSubject->status == 1) ? 'selected' : ''
                                }}>Activée
                            </option>
                            <option value="0" name="status" {{ ($getClassSubject->status == 0) ? 'selected' : ''
                                }}>Désactivée
                            </option>
                        </select>
                    </div>
                    <button type="submit"
                            class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded text-sm px-5 py-2.5 text-center transition-all duration-700 ease-out w-full">
                        Modifier
                    </button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

