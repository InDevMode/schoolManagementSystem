@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg dark:border-gray-700 mt-14">
        @include('message')
        <div class="flex justify-between pt-2">
            <div class="space-x-2 font-semibold">
                <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-clock"></i></span>
                <span>/</span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                        href="{{ url('admin/dashboard') }}">Dashboard</a></span>
                <span>/</span>
                <span>Horaire de Cours</span>
            </div>
        </div>

        <form action="" method="post"
              class="my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
            {{ csrf_field() }}

            <div class="grid grid-cols-3 gap-x-2">

                <div class="flex mb-5">
                    <select id="class_id" name="class_id"
                            class="class_id rounded-full bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                            required>
                        <option disabled selected>Choisissez la classe pour laquelle vous souhaitez définir un horaire
                        </option>
                        @foreach($getClass as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex mb-5">
                    <select id="subject_id" name="subject_id"
                            class="subject_id rounded-full bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                            required>
                        <option disabled selected>Choisissez la matière pour laquelle vous souhaitez définir un
                            horaire
                        </option>

                    </select>
                </div>
                <!-- Boutons -->

                <div class="w-full">
                    <a href="{{ url('admin/class_timetable/add') }}"
                       class="block text-gray-800 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                        Réinitialiser les filtres
                    </a>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".class_id").forEach(function (element) {
            element.addEventListener("change", function () {
                const class_id = this.value;

                fetch("/admin/class_timetable/subject", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        "_token": "{{ csrf_token() }}",
                        class_id: class_id
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Erreur serveur : " + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        let subjectDropdown = document.getElementById("subject_id"); // Sélectionne le dropdown
                        subjectDropdown.innerHTML = ""; // Vide les anciennes options

                        // Ajoute l'option par défaut
                        let defaultOption = document.createElement("option");
                        defaultOption.disabled = true;
                        defaultOption.selected = true;
                        defaultOption.textContent = "Choisissez la matière pour laquelle vous souhaitez définir un horaire";
                        subjectDropdown.appendChild(defaultOption);

                        // Vérifie si des matières sont retournées
                        if (data.subjects && data.subjects.length > 0) {
                            data.subjects.forEach(subject => {
                                let option = document.createElement("option");
                                option.value = subject.id;
                                option.textContent = subject.name;
                                subjectDropdown.appendChild(option);
                            });
                        } else {
                            // Si aucune matière trouvée
                            let noDataOption = document.createElement("option");
                            noDataOption.disabled = true;
                            noDataOption.textContent = "Aucune matière disponible pour cette classe";
                            subjectDropdown.appendChild(noDataOption);
                        }
                    })
                    .catch(error => {
                        console.error("Erreur javascript :", error);
                    });
            });
        });
    });
</script>


