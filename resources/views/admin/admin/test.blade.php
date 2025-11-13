@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4">
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

            <!-- Section Table -->
            <x-table.index :columns="[
                'Nom' => true,
                'Prénoms' => true,
                'Email' => true,
                'Statut' => true,
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
                        @foreach ($getAdmin as $admin)
                              <x-table.tr
                                    x-show="!search
                      || '{{ strtolower($admin->name) }}'.includes(search.toLowerCase())
                      || '{{ strtolower($admin->last_name) }}'.includes(search.toLowerCase())
                      || '{{ strtolower($admin->email) }}'.includes(search.toLowerCase())
                      || '{{ strtolower($admin->statut) }}'.includes(search.toLowerCase())
                      || '{{ strtolower($admin->created_at) }}'.includes(search.toLowerCase())
                      || '{{ strtolower($admin->updated_at) }}'.includes(search.toLowerCase())
                      ">

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
                                                <i
                                                      class="fa-solid fa-circle {{ $admin->status == 1 ? 'text-emerald-400' : 'text-red-600' }} mr-2"></i>
                                                {{ $admin->status == 1 ? 'Actif' : 'Inactif' }}
                                          </span>
                                    </x-table.td>
                                    <x-table.td
                                          x-show="visibleColumns['Date de Création']">{{ \Carbon\Carbon::parse($admin->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}</x-table.td>
                                    <x-table.td
                                          x-show="visibleColumns['Date de Modification']">{{ \Carbon\Carbon::parse($admin->updated_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}</x-table.td>

                                    {{-- Dropdown Actions --}}
                                    <x-table.td align="right">
                                          <x-table.actions-dropdown :id="$admin->id">
                                                {{-- Lien Profile avec modal --}}
                                                <div @click.stop>
                                                      <x-modal.profile :admin="$admin">
                                                            <x-slot:trigger>
                                                                  <x-link icon="fa-solid fa-user text-blue-500 py-2"
                                                                        hover="hover:text-blue-500 dark:hover:text-blue-500">
                                                                        Profile
                                                                  </x-link>
                                                            </x-slot:trigger>
                                                      </x-modal.profile>
                                                </div>

                                                {{-- Lien Message --}}
                                                <x-link href="{{ url('chat?receiver_id=' . base64_encode($admin->id)) }}"
                                                      icon="fa-solid fa-envelope text-indigo-500 py-2"
                                                      hover="hover:text-indigo-500 dark:hover:text-indigo-500">
                                                      Message
                                                </x-link>

                                                {{-- Lien Modifier --}}
                                                <x-link href="{{ url('admin/admin/edit', $admin->id) }}"
                                                      icon="fa-solid fa-edit text-emerald-500 py-2"
                                                      hover="hover:text-emerald-500 dark:hover:text-emerald-500">
                                                      Modifier
                                                </x-link>

                                                {{-- Lien Supprimer avec modal de confirmation --}}
                                                <div @click.stop>
                                                      <x-modal.confirm title="Supprimer l'administrateur"
                                                            confirmUrl="{{ url('admin/admin/delete', $admin->id) }}"
                                                            confirmLabel="Oui, supprimer" confirmVariant="danger">
                                                            <x-slot:trigger>
                                                                  <x-link icon="fa-solid fa-trash text-red-500 py-2"
                                                                        hover="hover:text-red-500 dark:hover:text-red-500">
                                                                        Supprimer
                                                                  </x-link>
                                                            </x-slot:trigger>
                                                            <p class="break-words whitespace-normal text-center">
                                                                  Êtes-vous sûr de vouloir supprimer l'administrateur
                                                                  <strong>{{ $admin->name }}
                                                                        {{ $admin->last_name }}</strong> ?
                                                                  Cette action est irréversible.
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
                        <x-table.footer :total="$getAdmin->total()" label="administrateur" :pagination="$getAdmin->links('vendor.pagination.tailwind')" />
                  </x-slot>

            </x-table.index>

      </div>
@endsection
