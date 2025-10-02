@extends('layouts.app')

@section('content')
      <div class="m-2">
            @include('message')
            <div class="container mx-auto px-4 py-8 max-w-6xl">

                  <x-pages.header title="Editer une période" subtitle="Modifiez les détails pour mettre à jour la période"
                        icon="fas fa-calendar-alt" :breadcrumbs="[
                            [
                                'url' => url('admin/dashboard'),
                                'label' => 'Tableau de bord',
                                'icon' => 'fas fa-home',
                            ],
                            [
                                'url' => url('admin/examinations/period/list'),
                                'label' => 'Liste des périodes',
                                'icon' => '',
                            ],
                            [
                                'url' => '#',
                                'icon' => '',
                                'label' => 'Modifier',
                            ],
                        ]" />

                  <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-700 transition-colors duration-300">
                        <div class="p-6 md:p-8">
                              <form action="{{ url('admin/examinations/period/edit/' . $getPeriod->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <x-form.input id="name" type="text" label="Titre de la période"
                                          placeholder="Entrez le titre de la période" icon="fas fa-tag"
                                          value="{{ old('name', $getPeriod->name) }}" required />

                                    <x-form.date id="start_date" label="Date de début" icon="fas fa-calendar-alt"
                                          value="{{ old('start_date', $getPeriod->start_date) }}" required />

                                    <x-form.date id="end_date" label="Date de fin" icon="fas fa-calendar-alt"
                                          value="{{ old('end_date', $getPeriod->end_date) }}" required />

                                    <x-form.select id="status" label="Statut" icon="fas fa-check-circle" :options="['1' => 'Actif', '0' => 'Inactif']"
                                          :selected="old('status', $getPeriod->status)" />

                                    @php
                                          $options = [];
                                          if ($getSettings) {
                                              $options[$getSettings->id] = $getSettings->school_name;
                                          }
                                    @endphp
                                    <x-form.select id="settings_id" label="Nom de l'école" icon="fas fa-check-circle"
                                          :options="$options" :selected="old('settings_id', $getPeriod->settings_id)" />

                                    <x-form.button type="submit" icon="mdi:pencil-outline" color="green">
                                          Modifier la période
                                    </x-form.button>
                              </form>
                        </div>
                  </div>
            </div>
      </div>
@endsection
