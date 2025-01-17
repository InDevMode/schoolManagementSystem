@extends('layouts.app')
@section('content')
<div class="p-4 mt-40 sm:ml-64 flex items-center justify-center">
    <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
        @include('message')
        <h2 class="bg-emerald-500 font-bold uppercase text-center text-white rounded-t-lg py-3 mb-5">
            Modifier ces assignations</h2>
        <form action="" method="post" class="p-5">
            {{ csrf_field() }}
            <div class="flex mb-5">
                <select id="class_id" name="class_id"
                        class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                        required>
                    <option selected>Choisissez la classe pour cette assignation</option>
                    @foreach($getClass as $class)
                    <option {{ $getClassSubject->class_id == $class->id ? 'selected' : '' }} value="{{ $class->id
                        }}">{{ $class->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex mb-5">
                <select multiple id="subject_id" name="subject_id[]"
                        class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                        required>
                    <option selected>Choisissez la ou les matières a assignée(s)</option>
                    @foreach($getSubject as $subject)
                        @php
                            $selected = "";
                        @endphp
                    @foreach($getAssignSubject as $getAssign)
                        @if($getAssign->subject_id == $subject->id)
                            @php
                                $selected = "selected";
                            @endphp
                        @endif
                    @endforeach
                    <option {{ $selected }} value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex mb-5">
                <select id="status" name="status"
                        class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                        required>
                    <option selected>Définissez un status pour cette assignation</option>
                    <option value="1" name="status" {{ $getClassSubject->status == 1 ? 'selected' : '' }}>Activée
                    </option>
                    <option value="0" name="status" {{ $getClassSubject->status == 0 ? 'selected' : '' }}>Désactivée
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
@endsection

