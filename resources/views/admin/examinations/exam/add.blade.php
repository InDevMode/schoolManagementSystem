@extends('layouts.app')
@section('content')
      <div class="m-2">
            @include('message')
            <div class="container mx-auto px-4 py-8 max-w-6xl">

                  <!-- Section Header -->
                  <x-pages.header title="Créer une nouvelle évaluation"
                        subtitle="Remplissez les détails pour créer une nouvelle
                        évaluation"
                        icon="fas fa-calendar-alt" :breadcrumbs="[
                            [
                                'url' => url('admin/dashboard'),
                                'label' => 'Tableau de bord',
                                'icon' => 'fas fa-home',
                            ],
                            [
                                'url' => url('admin/examinations/exam/list'),
                                'label' => 'Liste des évaluations',
                                'icon' => '',
                            ],
                            [
                                'url' => '#',
                                'icon' => '',
                                'label' => 'Nouvelle',
                            ],
                        ]" />

                  <!-- Main Form Section -->
                  <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-700 transition-colors duration-300">
                        <div class="p-6 md:p-8">
                              <form action="{{ url('admin/examinations/exam/add') }}" method="post"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}

                                    <x-form.input id="name" type="text" label="Titre de l'évaluation"
                                          placeholder="Entrez le titre de l'évaluation" icon="fas fa-tag"
                                          value="{{ old('name') }}" required />

                                    <x-form.date id="start_date" label="Date de début" icon="fas fa-calendar-alt"
                                          value="{{ old('start_date') }}" required />

                                    <x-form.date id="end_date" label="Date de fin" icon="fas fa-calendar-alt"
                                          value="{{ old('end_date') }}" required />

                                    @php
                                          $options = [];
                                          foreach ($getPeriods as $key => $getPeriod) {
                                              $options[$getPeriod->id] = $getPeriod->name;
                                          }
                                    @endphp

                                    <x-form.select id="period_id" label="Titre de la période" icon="fas fa-check-circle"
                                          :options="$options" selected="" />

                                    <x-form.select id="status" label="Statut" icon="fas fa-check-circle"
                                          value="{{ old('status') }}" :options="['1' => 'Actif', '0' => 'Inactif']" selected="" />


                                    <x-form.button icon="mdi:content-save-check-outline">
                                          Créer une nouvelle évaluation
                                    </x-form.button>

                              </form>
                        </div>
                  </div>
            </div>
      @endsection
