@extends('layouts.app')
@section('content')
<div class="m-5">
    <main>
        <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
            <div class="mx-auto max-w-242.5">
                <div
                    class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <h2 class="uppercase font-bold text-black dark:text-bodydark">
                        Créer une note
                    </h2>
                    <nav>
                        <ol class="flex items-center gap-2">
                            <li>
                                <span class="font-medium text-violet-600"><i class="fa-solid fa-note-sticky"></i></span>
                            </li>
                            <li>
                                /<a class="font-medium hover:text-violet-600 transition duration-300"
                                    href="{{ url('admin/examinations/marks_grade/list') }}"> Note</a>
                            </li>
                        </ol>
                    </nav>
                </div>
                @include('message')
                <div class="flex flex-col gap-9">
                    <!-- Contact Form -->
                    <div
                        class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
                    >
                        <form action="{{ url('admin/examinations/marks_grade/add') }}" method="post"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="p-6.5">
                                <div class="mb-4.5">
                                    <div>
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Nom
                                        </label>
                                        <input
                                            type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Entrez un nom" required
                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                                        />
                                    </div>
                                </div>
                                <div class="mb-4.5">
                                    <div>
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Pourcentage de
                                        </label>
                                        <input
                                            type="number" id="percent_from" name="percent_from" value="{{ old('percent_from') }}" placeholder="Entrez un pourcentage" required
                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                                        />
                                    </div>
                                </div>
                                <div class="mb-4.5">
                                    <div>
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Pourcentage à
                                        </label>
                                        <input
                                            type="number" id="percent_to" name="percent_to" value="{{ old('percent_to') }}" placeholder="Entrez un pourcentage" required
                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                                        />
                                    </div>
                                </div>

                                <button type="submit"
                                        class="flex w-full justify-center rounded-lg bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90"
                                >
                                    Créer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
<script>

</script>

