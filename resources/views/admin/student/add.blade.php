@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="space-x-2 font-semibold">
            <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-user-graduate"></i></span>
            <span>/</span>
            <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                    href="{{ url('admin/student/list') }}">Liste des élèves</a></span>
            <span>/</span>
            <span>Elève</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-7xl bg-white shadow-lg mt-2 rounded-md">
                @include('message')
                <form action="{{ url('admin/student/add') }}" method="post" class="p-5" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <h2 class="font-bold uppercase text-center text-white rounded-t-md bg-violet-500 py-3 mb-5">
                        Créer un élève</h2>
                    <div class="grid grid-cols-2 gap-x-5">
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="name">Nom<span
                                    class="text-red-500 font-bold">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="nom..." required>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="last_name">Prénom<span
                                    class="text-red-500 font-bold">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="prénom..." required>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="admission_number">Numéro
                                d'admission<span class="text-red-500 font-bold">*</span></label>
                            <input type="text" id="admission_number" name="admission_number"
                                   value="{{ old('admission_number') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="numéro d'admission..." required>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="roll_number">Numéro de
                                Rôle</label>
                            <input type="text" id="roll_number" name="roll_number" value="{{ old('roll_number') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="numéro de rôle...">
                        </div>
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="class_id">Classe<span
                                    class="text-red-500 font-bold">*</span></label>
                            <select id="class_id" name="class_id"
                                    class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                    required>
                                <option disabled>Attribuez une classe à cet élève</option>
                                @foreach($getClass as $class)
                                <option value="{{ $class->id }}">{{ $class->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="gender">Genre<span
                                    class="text-red-500 font-bold">*</span></label>
                            <select id="gender" name="gender"
                                    class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                    required>
                                <option disabled>Choisissez un genre à cet élève</option>
                                <option {{ (old(
                                'gender') == 'male') ? 'selected' : '' }} value="male">Masculin</option>
                                <option {{ (old(
                                'gender') == 'female') ? 'selected' : '' }} value="female">Féminin</option>
                                <option {{ (old(
                                'gender') == 'other') ? 'selected' : '' }} value="other">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="date_of_bith">Date
                                de Naissance<span class="text-red-500 font-bold">*</span></label>
                            <input type="date" id="date_of_birth" name="date_of_birth"
                                   value="{{ old('date_of_birth') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="Date d'anniversaire..." required>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="caste">Caste</label>
                            <input type="text" id="caste" name="caste" value="{{ old('caste') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="Caste...">
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="religion">Religion</label>
                            <input type="text" id="religion" name="religion" value="{{ old('religion') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="Religion...">
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="mobile_number">Numéro de
                                téléphone</label>
                            <input type="text" id="mobile_number" name="mobile_number"
                                   value="{{ old('mobile_number') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="Numéro de téléphone...">
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="admission_date">Date
                                d'admission<span class="text-red-500 font-bold">*</span></label>
                            <input type="date" id="admission_date" name="admission_date"
                                   value="{{ old('admission_date') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="Date d'admission..." required>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="profile_picture">Photo de
                                Profile</label>
                            <input type="file" id="profile_picture" name="profile_picture"
                                   value=""
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="Photo de profile...">
                        </div>
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="blood_group">Groupe
                                Sanguin</label>
                            <select id="blood_group" name="blood_group"
                                    class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3">
                                <option disabled>Attribuez un group sanguin pour cet élève</option>
                                <option {{ (old(
                                'blood_group') == 'a+') ? 'selected' : '' }} value="a+">A+</option>
                                <option {{ (old(
                                'blood_group') == 'a-') ? 'selected' : '' }} value="a-">A-</option>
                                <option {{ (old(
                                'blood_group') == 'b+') ? 'selected' : '' }} value="b+">B+</option>
                                <option {{ (old(
                                'blood_group') == 'b-') ? 'selected' : '' }} value="b-">B-</option>
                                <option {{ (old(
                                'blood_group') == 'ab+') ? 'selected' : '' }} value="ab+">AB+</option>
                                <option {{ (old(
                                'blood_group') == 'ab-') ? 'selected' : '' }} value="ab-">AB-</option>
                                <option {{ (old(
                                'blood_group') == 'o+') ? 'selected' : '' }} value="o+">O+</option>
                                <option {{ (old(
                                'blood_group') == 'o-') ? 'selected' : '' }} value="o-">O-</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="height">Taille</label>
                            <input type="text" id="height" name="height" value="{{ old('height') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="Taille...">
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="weight">Poids</label>
                            <input type="text" id="weight" name="weight" value="{{ old('weight') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="Poids...">
                        </div>
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="status">Status<span
                                    class="text-red-500 font-bold">*</span></label>
                            <select id="status" name="status"
                                    class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                    required>
                                <option disabled>Définissez un status pour cet élève</option>
                                <option {{ (old(
                                'status') == '1') ? 'selected' : '' }} value="1">Activé</option>
                                <option {{ (old(
                                'status') == '0') ? 'selected' : '' }} value="0">Désactivé</option>
                            </select>
                        </div>
                    </div>
                    <div class="border-t-2 border-gray-300 mt-3 mb-5 pt-3">
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="email">Email<span
                                    class="text-red-500 font-bold">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="email..." required>
                        </div>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="password">Mot de
                                passe<span class="text-red-500 font-bold">*</span></label>
                            <input type="password" id="password" name="password" value="{{ old('password') }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="mot de passe..." required>
                        </div>
                    </div>
                    <div>
                        <button type="submit"
                                class="text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out">
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

