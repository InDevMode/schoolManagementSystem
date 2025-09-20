@extends('layouts.app')

@section('content')
      <div class="container mx-auto">
            @include('message')

            <!-- Section Header -->
            <x-pages.header title="Liste des évaluations" subtitle="Gérez les évalutations de votre plateforme"
                  icon="fa-solid fa-square-poll-horizontal" :breadcrumbs="[
                      [
                          'url' => url('admin/dashboard'),
                          'label' => 'Tableau de bord',
                          'icon' => 'fas fa-home',
                      ],
                      [
                          'url' => url('admin/examinations/exam/add'),
                          'label' => 'Créer une évaluation',
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
                'Evaluation' => true,
                'Période' => true,
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
                        @foreach ($getExams as $exams)
                              <x-table.tr
                                    x-show="!search
                        || '{{ strtolower($exams->name) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($exams->periods_name) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($exams->start_date) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($exams->end_date) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($exams->statut) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($exams->created_by_name) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($exams->created_at) }}'.includes(search.toLowerCase())
                        || '{{ strtolower($exams->updated_at) }}'.includes(search.toLowerCase())">
                                    {{-- Case à cocher par ligne --}}
                                    <x-table.td align="center" class="w-10">
                                          <x-table.checkbox :id="$exams->id" class="row-check" />
                                    </x-table.td>

                                    {{-- Colonnes dynamiques --}}
                                    <x-table.column label="Evaluation" :value="$exams->name" />
                                    <x-table.column label="Période" :value="$exams->periods_name" />
                                    <x-table.column label="Date de début" :value="\Carbon\Carbon::parse($exams->start_date)
                                        ->locale('fr')
                                        ->translatedFormat('d M Y')" />
                                    <x-table.column label="Date de fin" :value="\Carbon\Carbon::parse($exams->end_date)
                                        ->locale('fr')
                                        ->translatedFormat('d M Y')" />
                                    <x-table.column label="Statut" :value="view('components.table.status', ['status' => $exams->status])" />
                                    <x-table.column label="Crée par" :value="$exams->created_by_name" />
                                    <x-table.column label="Date de Création" :value="\Carbon\Carbon::parse($exams->created_at)
                                        ->locale('fr')
                                        ->translatedFormat('d M Y H:i:s')" />
                                    <x-table.column label="Date de Modification" :value="\Carbon\Carbon::parse($exams->updated_at)
                                        ->locale('fr')
                                        ->translatedFormat('d M Y H:i:s')" />

                                    {{-- Actions --}}
                                    <x-table.td align="right">
                                          <x-table.actions :id="$exams->id" :edit-url="url('admin/examinations/exam/edit', $exams->id)" :delete-url="url('admin/examinations/exam/delete', $exams->id)"
                                                delete-message="Êtes-vous sûr de vouloir supprimer la période {{ $exams->name }} ?" />
                                    </x-table.td>
                              </x-table.tr>
                        @endforeach
                  </x-table.tbody>

                  {{-- Footer --}}
                  <x-slot name="footer">
                        <x-table.footer :total="$getExams->total()" label="période" :pagination="$getExams->links('vendor.pagination.tailwind')" />
                  </x-slot>

            </x-table.index>
      </div>
@endsection
