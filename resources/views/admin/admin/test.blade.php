@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4 py-5">
            @include('message')

            <!-- Section Header -->
            <x-pages.header title="Liste des administrateurs" subtitle="Gérez les comptes administrateurs de votre plateforme"
                  icon="fas fa-user-shield" :breadcrumbs="[
                      [
                          'url' => url('admin/dashboard'),
                          'label' => 'Tableau de bord',
                          'icon' => 'fas fa-home',
                      ],
                      [
                          'url' => url('admin/admin/add'),
                          'label' => 'Créer un administrateur',
                          'icon' => 'fas fa-plus-circle',
                      ],
                  ]" />

            <!-- Section Filtrage -->
            <x-filter.section>

                  <x-filter.field id="name" label="Nom" icon="fas fa-user" />
                  <x-filter.field id="last_name" label="Prénoms" icon="fas fa-user-tag" />
                  <x-filter.field id="email" label="Email" type="email" icon="fas fa-envelope" />
                  <x-filter.field id="status" label="Statut" type="select" icon="fas fa-check-circle" :options="['1' => 'Actif', '0' => 'Inactif']" />
                  <x-filter.field id="gender" label="Genre" type="select" icon="fas fa-venus-mars" :options="['male' => 'Masculin', 'female' => 'Féminin', 'other' => 'Autre']" />
                  <x-filter.field id="mobile_number" label="Téléphone" icon="fas fa-phone" />
                  <x-filter.field id="address" label="Adresse" icon="fas fa-map-marker-alt" />
                  <x-filter.field id="occupation" label="Occupation" icon="fas fa-briefcase" />
                  <x-filter.field id="created_at" label="Date de création" type="date" icon="fas fa-calendar-plus" />
                  <x-filter.field id="updated_at" label="Date de modification" type="date" icon="fas fa-calendar-check" />

                  <x-filter.actions resetUrl="{{ url('admin/admin/list') }}" />

            </x-filter.section>

            <!-- Section Table -->
            <x-table.index :columns="['Nom' => true, 'Prénoms' => true, 'Email' => true, 'Statut' => true]">

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
                        @foreach ($getAdmin as $admin)
                              <x-table.tr
                                    x-show="!search
                                    || '{{ strtolower($admin->name) }}'.includes(search.toLowerCase())
                                    || '{{ strtolower($admin->last_name) }}'.includes(search.toLowerCase())
                                    || '{{ strtolower($admin->email) }}'.includes(search.toLowerCase())">

                                    {{-- Case à cocher par ligne --}}
                                    <x-table.td align="center" class="w-10">
                                          <input type="checkbox" class="row-check check-custom" value="{{ $admin->id }}"
                                                @change="
                                                    if ($event.target.checked) {
                                                        if (!selectedIds.includes('{{ $admin->id }}'))
                                                            selectedIds.push('{{ $admin->id }}')
                                                    } else {
                                                        selectedIds = selectedIds.filter(id => id !== '{{ $admin->id }}')
                                                    }
                                     ">
                                    </x-table.td>

                                    {{-- Colonnes dynamiques --}}
                                    <x-table.td x-show="visibleColumns['Nom']">{{ $admin->name }}</x-table.td>
                                    <x-table.td x-show="visibleColumns['Prénoms']">{{ $admin->last_name }}</x-table.td>
                                    <x-table.td x-show="visibleColumns['Email']">{{ $admin->email }}</x-table.td>
                                    <x-table.td x-show="visibleColumns['Statut']">
                                          <span class="flex items-center">
                                                <i  class="fa-solid fa-circle {{ $admin->status == 1 ? 'text-emerald-400' : 'text-red-600' }} mr-2"></i>
                                                {{ $admin->status == 1 ? 'Actif' : 'Inactif' }}
                                          </span>
                                    </x-table.td>

                                    {{-- Dropdown Actions --}}
                                    <x-table.td align="right">
                                          <x-table.actions-dropdown :id="$admin->id">
                                                <a href=""
                                                      class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Voir</a>
                                                <a href=""
                                                      class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Éditer</a>
                                          </x-table.actions-dropdown>
                                    </x-table.td>
                              </x-table.tr>
                        @endforeach
                  </x-table.tbody>

                  {{-- Footer optionnel --}}
                  <x-slot name="footer">
                        <x-table.footer :total="$getAdmin->count()" label="administrateurs" :pagination="$getAdmin->links('vendor.pagination.tailwind')" />
                  </x-slot>

            </x-table.index>
      </div>
@endsection

<script></script>
