@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="flex justify-between pt-2">
            <div class="space-x-2 font-semibold mt-3">
                <span class="text-emerald-500"><i class="fa-solid fa-person-breastfeeding"></i></span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span class="hover:underline hover:text-emerald-500 transition-all duration-300"><a
                        href="{{ url('parent/dashboard') }}">Dashboard</a></span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
                <span>Mon Compte</span>
            </div>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-7xl bg-white shadow-xl mt-2 rounded-md">
                @include('message')
                <form action="" method="post" class="p-5" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <h2 class="font-bold uppercase text-center text-white rounded-t-md bg-emerald-500 py-3 mb-5">
                        Modifier mes informations personnelles</h2>
                    <div class="mb-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="profile_picture">Photo de
                            Profile</label>
                        <input type="file" id="profile_picture" name="profile_picture"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="Photo de profile...">
                        <img class="h-auto max-w-[100px] rounded-full" src="{{ $profile_picture_url }}"
                             alt="Photo de profile">
                    </div>
                    <div class="grid grid-cols-2 gap-x-5">
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="name">Nom<span
                                    class="text-red-500 font-bold">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $getUserData->name) }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="nom..." required>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="last_name">Prénom<span
                                    class="text-red-500 font-bold">*</span></label>
                            <input type="text" id="last_name" name="last_name"
                                   value="{{ old('last_name', $getUserData->last_name) }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="prénom..." required>
                        </div>
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="gender">Genre<span
                                    class="text-red-500 font-bold">*</span></label>
                            <select id="gender" name="gender"
                                    class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                    required>
                                <option disabled selected>Choisissez un genre à cet parent</option>
                                <option {{ (old(
                                'gender', $getUserData->gender) == 'male') ? 'selected' : '' }}
                                value="male">Masculin</option>
                                <option {{ (old(
                                'gender', $getUserData->gender) == 'female') ? 'selected' : '' }}
                                value="female">Féminin</option>
                                <option {{ (old(
                                'gender', $getUserData->gender) == 'other') ? 'selected' : '' }}
                                value="other">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900"
                                   for="occupation">Occupation<span class="text-red-500 font-bold">*</span></label>
                            <input type="text" id="occupation" name="occupation"
                                   value="{{ old('occupation', $getUserData->occupation) }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="occupation..." required>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="mobile_number">Numéro de
                                téléphone<span class="text-red-500 font-bold">*</span></label>
                            <input type="text" id="mobile_number" name="mobile_number"
                                   value="{{ old('mobile_number', $getUserData->mobile_number) }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="Numéro de téléphone..." required>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="address">Addresse<span
                                    class="text-red-500 font-bold">*</span></label>
                            <input type="text" id="address" name="address"
                                   value="{{ old('address', $getUserData->address) }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="addresse...">
                        </div>
                    </div>
                    <div class="border-t-2 border-gray-300 mt-3 mb-5 pt-3">
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="email">Email<span
                                    class="text-red-500 font-bold">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $getUserData->email) }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="email..." required>
                        </div>
                    <div>
                        <button type="submit"
                                class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out">
                            Modifier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

