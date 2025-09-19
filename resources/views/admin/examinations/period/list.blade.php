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
                                    <input type="checkbox" class="check-custom" @change="toggleAll($el, $event.target.checked)"
                                          title="Tout sélectionner">
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
                                    || '{{ strtolower($period->updated_at) }}'.includes(search.toLowerCase())
                                    ">

                                    {{-- Case à cocher par ligne --}}
                                    <x-table.td align="center" class="w-10">
                                          <input type="checkbox" class="row-check check-custom" value="{{ $period->id }}"
                                                @change="
                                                    if ($event.target.checked) {
                                                        if (!selectedIds.includes('{{ $period->id }}'))
                                                            selectedIds.push('{{ $period->id }}')
                                                    } else {
                                                        selectedIds = selectedIds.filter(id => id !== '{{ $period->id }}')
                                                    }
                                     ">
                                    </x-table.td>

                                    {{-- Colonnes dynamiques --}}
                                    <x-table.td x-show="visibleColumns['Titre']">{{ $period->name }}</x-table.td>
                                    <x-table.td
                                          x-show="visibleColumns['École']">{{ $period->settings_school_name }}</x-table.td>
                                    <x-table.td
                                          x-show="visibleColumns['Date de début']">{{ \Carbon\Carbon::parse($period->start_date)->locale('fr')->translatedFormat('d M Y') }}</x-table.td>
                                    <x-table.td
                                          x-show="visibleColumns['Date de fin']">{{ \Carbon\Carbon::parse($period->end_date)->locale('fr')->translatedFormat('d M Y') }}</x-table.td>
                                    <x-table.td x-show="visibleColumns['Statut']">
                                          <span class="flex items-center">
                                                <i
                                                      class="fa-solid fa-circle {{ $period->status == 1 ? 'text-emerald-400' : 'text-red-600' }} mr-2"></i>
                                                {{ $period->status == 1 ? 'Actif' : 'Inactif' }}
                                          </span>
                                    </x-table.td>
                                    <x-table.td
                                          x-show="visibleColumns['Crée par']">{{ $period->created_by_name }}</x-table.td>
                                    <x-table.td
                                          x-show="visibleColumns['Date de Création']">{{ \Carbon\Carbon::parse($period->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}</x-table.td>
                                    <x-table.td
                                          x-show="visibleColumns['Date de Modification']">{{ \Carbon\Carbon::parse($period->updated_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}</x-table.td>

                                    {{-- Dropdown Actions --}}
                                    <x-table.td align="right">
                                          <x-table.actions-dropdown :id="$period->id">
                                                <x-link href="{{ url('admin/examinations/period/edit', $period->id) }}"
                                                      icon="fa-solid fa-edit text-emerald-500 py-2"
                                                      hover="hover:text-emerald-500">Modifier</x-link>

                                                {{-- Use the confirmation component for the delete action --}}
                                                <div @click.stop>
                                                      <x-modal.confirm title="Supprimer une période"
                                                            confirmUrl="{{ url('admin/examinations/period/delete', $period->id) }}">

                                                            <x-slot:trigger>
                                                                  <x-link icon="fa-solid fa-trash text-red-500 py-2">
                                                                        Supprimer
                                                                  </x-link>
                                                            </x-slot:trigger>

                                                            <p class="break-words whitespace-normal text-center">
                                                                  {{-- Your message content goes here. It will be wrapped correctly. --}}
                                                                  Êtes-vous sûr de vouloir supprimer la période
                                                                  '{{ $period->name }}' ? Cette action est irréversible.
                                                            </p>

                                                      </x-modal.confirm>
                                                </div>

                                          </x-table.actions-dropdown>
                                    </x-table.td>
                              </x-table.tr>
                        @endforeach
                  </x-table.tbody>

                  {{-- Footer optionnel --}}
                  <x-slot name="footer">
                        <x-table.footer :total="$getPeriods->total()" label="periode" :pagination="$getPeriods->links('vendor.pagination.tailwind')" />
                  </x-slot>

            </x-table.index>

      </div>
@endsection

<script></script>
