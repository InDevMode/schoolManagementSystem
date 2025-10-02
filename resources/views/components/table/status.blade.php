<!-- resources/views/components/table/status.blade.php -->
@props(['status' => 0])

<span class="flex items-center">
      <i class="fa-solid fa-circle {{ $status == 1 ? 'text-emerald-500' : 'text-red-500' }} mr-2"></i>
      {{ $status == 1 ? 'Actif' : 'Inactif' }}
</span>
