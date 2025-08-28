@props([
    'id',
    'label' => null,
    'placeholder' => '',
    'icon' => null,
    'required' => false
])

<div class="mb-6 w-full">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }} @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        {{-- Icône gauche si définie --}}
        @if ($icon)
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <iconify-icon icon="{{ $icon }}" width="20" height="20"></iconify-icon>
            </span>
        @endif

        <input type="password" id="{{ $id }}" name="{{ $id }}"
               placeholder="{{ $placeholder }}"
               @if ($required) required @endif
               {{ $attributes->merge([
                   'class' => 'h-11 w-full rounded-lg border border-gray-300 bg-gray-50
                               px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400
                               focus:border-violet-300 focus:ring-3 focus:ring-violet-500/10
                               dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/40
                               dark:focus:border-violet-800 transition-all duration-200 ' .
                               ($icon ? 'pl-10' : '')
               ]) }}>

        {{-- Toggle œil droit --}}
        <span class="absolute right-4 top-3 cursor-pointer" onclick="togglePasswordVisibility('{{ $id }}')">
            <iconify-icon icon="mdi:eye" id="togglePasswordIcon-{{ $id }}" width="20" height="20"></iconify-icon>
        </span>
    </div>
</div>

<script>
    function togglePasswordVisibility(id) {
        const passwordInput = document.getElementById(id);
        const icon = document.getElementById('togglePasswordIcon-' + id);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.setAttribute('icon', 'mdi:eye-off');
        } else {
            passwordInput.type = 'password';
            icon.setAttribute('icon', 'mdi:eye');
        }
    }
</script>
