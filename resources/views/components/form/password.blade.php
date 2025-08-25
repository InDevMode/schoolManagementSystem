@props([
    'id' => 'password',
    'name' => 'password',
    'label' => 'Mot de passe',
    'placeholder' => 'Entrez un mot de passe',
    'required' => false,
])

<div class="mb-6 w-full">
      <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }} @if ($required)
                  <span class="text-red-500">*</span>
            @endif
      </label>
      <div class="relative">
            <input type="password" id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}"
                  @if ($required) required @endif
                  {{ $attributes->merge([
                      'class' => 'form-password w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50
                                          focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                          dark:bg-gray-700 dark:border-gray-600 dark:text-white
                                          dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200',
                  ]) }}>
            <span class="absolute right-4 top-4 cursor-pointer" onclick="togglePasswordVisibility()">
                  <span class="text-[24px] text-violet-600">
                        <iconify-icon icon="mdi:lock" id="togglePasswordIcon"></iconify-icon>
                  </span>
            </span>
      </div>
</div>

<script>
      function togglePasswordVisibility() {
            const passwordInput = document.getElementById('{{ $id }}');
            const icon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                  passwordInput.type = 'text';
                  icon.setAttribute('icon', 'mdi:lock-open');
            } else {
                  passwordInput.type = 'password';
                  icon.setAttribute('icon', 'mdi:lock');
            }
      }
</script>
