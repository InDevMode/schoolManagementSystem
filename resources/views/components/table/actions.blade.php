<!-- resources/views/components/table/actions.blade.php -->
@props(['id', 'editUrl', 'deleteUrl', 'deleteLabel' => 'Supprimer', 'deleteMessage' => null])

<x-table.actions-dropdown :id="$id">
    <x-link :href="$editUrl" icon="fa-solid fa-edit text-emerald-500 py-2"
        hover="hover:text-emerald-500 dark:hover:text-emerald-500">
        Modifier
    </x-link>

    <div @click.stop>
        <x-modal.confirm title="{{ $deleteLabel }}" confirmUrl="{{ $deleteUrl }}">
            <x-slot:trigger>
                <x-link icon="fa-solid fa-trash text-red-500 py-2"
                    hover="hover:text-red-500 dark:hover:text-red-500">
                    {{ $deleteLabel }}
                </x-link>
            </x-slot:trigger>
            <p class="break-words whitespace-normal text-center">
                {{ $deleteMessage }}
            </p>
        </x-modal.confirm>
    </div>
</x-table.actions-dropdown>
