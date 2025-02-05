@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
    >
        <h2 class="text-title-md uppercase font-bold text-black dark:text-white">
            Liste des administrateurs
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <a class="font-medium" href="{{ url('admin/dashboard') }}">Dashboard</a>
                </li>
            </ol>
        </nav>
    </div>
    <div class="">
        <div class="mt-4">
            {{ $getAdmin->links('vendor.pagination.tailwind') }}
        </div>
    </div>
    <div
        class="rounded-sm border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1"
    >

        <div class="flex flex-col">
            <div
                class="grid grid-cols-3 rounded-sm bg-gray-2 dark:bg-meta-4 sm:grid-cols-7"
            >
                <div class="p-2.5 xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Nom</h5>
                </div>
                <div class="p-2.5 text-center xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Prénoms</h5>
                </div>
                <div class="p-2.5 text-center xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Email</h5>
                </div>
                <div class="hidden p-2.5 text-center sm:block xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Status</h5>
                </div>
                <div class="hidden p-2.5 text-center sm:block xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Date de Création</h5>
                </div>
                <div class="hidden p-2.5 text-center sm:block xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Date de Modification</h5>
                </div>
                <div class="hidden p-2.5 text-center sm:block xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Actions</h5>
                </div>
            </div>

            @foreach($getAdmin as $index => $user)
            <div class="grid grid-cols-3 sm:grid-cols-7">
                <div class="flex items-center gap-3 p-2.5 xl:p-5">
                    <p class="hidden font-medium text-black dark:text-white sm:block">
                        {{ $user -> name }}
                    </p>
                </div>
                <div class="flex items-center justify-center p-2.5 xl:p-5">
                    <p class="font-medium text-black dark:text-white">  {{ $user -> last_name }}</p>
                </div>

                <div class="flex items-center justify-center p-2.5 xl:p-5">
                    <p class="font-medium text-meta-3 me-2 px-2.5 py-0.5 rounded border border-gray-400">{{ $user -> email }}</p>
                </div>

                @if($user->status == 0)
                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="font-medium text-black dark:text-white">
                    <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div>
                    Inactif
                    </p>
                </div>
                @elseif($user->status == 1)
                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="font-medium text-black dark:text-white">
                    <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 me-2"></div>
                    Actif
                    </p>
                </div>
                @endif

                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="font-medium text-meta-5"> {{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i:s') }}</p>
                </div>
                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <p class="font-medium text-meta-5">{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i:s') }}</p>
                </div>
                <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                    <a href="{{ url('admin/admin/edit', $user -> id) }}" class="font-medium hover:text-violet-600"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="{{ url('admin/admin/delete', $user -> id) }}" class="font-medium hover:text-red-600 ms-3"><i class="fa-solid fa-trash"></i></a>
                </div>
            </div>
            @endforeach
            @if($getAdmin->isEmpty())
            <div class="grid grid-cols-3 sm:grid-cols-7">
                <div> Aucun administrateur trouvé.</div>
            </div>
            @endif
            <div
                class="mb-6 mt-3 border-t border-gray-200 pt-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <h2 class="text-title-sm uppercase font-bold text-black dark:text-white">
                   Total des administrateurs
                </h2>
                <nav>
                    <ol class="flex items-center gap-2">
                        <li>
                            <p class="text-md font-medium">{{ $getAdmin->total() }}</p>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection



