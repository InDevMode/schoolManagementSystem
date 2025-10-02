<!-- resources/views/components/table/checkbox.blade.php -->
@props(['id' => null])

<input type="checkbox" value="{{ $id }}" {{ $attributes->merge(['class' => 'check-custom']) }}
      @change="
        if ($event.target.checked) {
            if (!selectedIds.includes('{{ $id }}')) selectedIds.push('{{ $id }}')
        } else {
            selectedIds = selectedIds.filter(i => i !== '{{ $id }}')
        }
    ">
