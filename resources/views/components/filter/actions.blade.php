{{-- resources/views/components/filter/actions.blade.php --}}
@props(['resetUrl' => url()->current()])

<div class="flex items-end gap-2">
      <button type="submit"
            class="w-full bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
            <i class="fas fa-search"></i>
            Rechercher
      </button>
      <a href="{{ $resetUrl }}"
            class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
            <i class="fas fa-sync-alt"></i>
            Réinitialiser
      </a>
</div>
