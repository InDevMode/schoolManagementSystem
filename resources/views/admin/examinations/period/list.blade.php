@extends('layouts.app')

@section('content')
      <div class="container mx-auto">
            @include('message')

            <!-- Section Header -->
            <x-pages.header title="Liste des périodes" subtitle="Gérez les périodes de votre plateforme"
                  icon="fas fa-calendar-alt" :breadcrumbs="[
                      [
                          'url' => url('admin/dashboard'),
                          'label' => 'Tableau de bord',
                          'icon' => 'fas fa-home',
                      ],
                      [
                          'url' => url('admin/examinations/period/add'),
                          'label' => 'Créer une période',
                          'icon' => 'fas fa-plus-circle',
                      ],
                      [
                          'url' => '#',
                          'label' => 'Liste',
                          'icon' => '',
                      ],
                  ]" />

            <!-- Section Table -->
            <x-table.index :columns="[
                'Titre' => true,
                'École' => true,
                'Date de début' => true,
                'Date de fin' => true,
                'Statut' => true,
                'Crée par' => true,
                'Date de Création' => true,
                'Date de Modification' => true,
            ]">

                  {{-- Thead --}}
                  <x-table.thead>
                        <x-table.tr>
                              {{-- Colonne "sélectionner tout" --}}
                              <x-table.th align="center" class="w-10">
                                    <x-table.checkbox @change="toggleAll($el, $event.target.checked)" />
                              </x-table.th>

                              {{-- Colonnes dynamiques --}}
                              <template x-for="(visible, col) in visibleColumns" :key="col">
                                    <x-table.th x-show="visible" x-text="col"></x-table.th>
                              </template>

                              {{-- Colonne fixe : Actions --}}
                              <x-table.th align="right">Actions</x-table.th>
                        </x-table.tr>
                  </x-table.thead>

                  {{-- Tbody --}}
                  <x-table.tbody>
                        @foreach ($getPeriods as $period)
                              <x-table.tr
                                    x-show="!search
                        || '{{ strtolower($period->name) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($period->settings_school_name) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($period->start_date) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($period->end_date) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($period->statut) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($period->created_by_name) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($period->created_at) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($period->updated_at) }}'.includes(search.toLowerCase())">
                                    {{-- Case à cocher par ligne --}}
                                    <x-table.td align="center" class="w-10">
                                          <x-table.checkbox :id="$period->id" class="row-check" />
                                    </x-table.td>

                                    {{-- Colonnes dynamiques --}}
                                    <x-table.column label="Titre" :value="$period->name" />
                                    <x-table.column label="École" :value="$period->settings_school_name" />
                                    <x-table.column label="Date de début" :value="\Carbon\Carbon::parse($period->start_date)
                                        ->locale('fr')
                                        ->translatedFormat('d M Y')" />
                                    <x-table.column label="Date de fin" :value="\Carbon\Carbon::parse($period->end_date)
                                        ->locale('fr')
                                        ->translatedFormat('d M Y')" />
                                    <x-table.column label="Statut" :value="view('components.table.status', ['status' => $period->status])" />
                                    <x-table.column label="Crée par" :value="$period->created_by_name" />
                                    <x-table.column label="Date de Création" :value="\Carbon\Carbon::parse($period->created_at)
                                        ->locale('fr')
                                        ->translatedFormat('d M Y H:i:s')" />
                                    <x-table.column label="Date de Modification" :value="\Carbon\Carbon::parse($period->updated_at)
                                        ->locale('fr')
                                        ->translatedFormat('d M Y H:i:s')" />

                                    {{-- Actions --}}
                                    <x-table.td align="right">
                                          <x-table.actions :id="$period->id" :edit-url="url('admin/examinations/period/edit', $period->id)" :delete-url="url('admin/examinations/period/delete', $period->id)"
                                                delete-message="Êtes-vous sûr de vouloir supprimer la période {{ $period->name }} ?" />
                                    </x-table.td>
                              </x-table.tr>
                        @endforeach
                  </x-table.tbody>

                  {{-- Footer --}}
                  <x-slot name="footer">
                        <x-table.footer :total="$getPeriods->total()" label="période" :pagination="$getPeriods->links('vendor.pagination.tailwind')" />
                  </x-slot>

            </x-table.index>
      </div>
@endsection
