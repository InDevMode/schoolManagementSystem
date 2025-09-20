<!-- resources/views/components/table/column.blade.php -->
@props(['label', 'value' => null])

<x-table.td x-show="visibleColumns['{{ $label }}']">
      {!! $value !!}
</x-table.td>
