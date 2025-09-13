@props([
    'id',
    'label',
    'icon' => null,
    'type' => 'text',
    'options' => [],
])

<div>
      <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
      </label>
      <div class="relative">
            @if ($icon)
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="{{ $icon }} text-gray-400"></i>
                  </div>
            @endif

            @if ($type === 'select')
                  <select id="{{ $id }}" name="{{ $id }}"
                        class="appearance-none {{ $icon ? 'pl-10' : '' }} w-full rounded-lg border border-gray-300 dark:border-gray-600
                       bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-600 focus:border-indigo-600 p-2.5 pr-8 outline-none">
                        <option value="" disabled selected>Veuillez choisir une option</option>
                        @foreach ($options as $key => $value)
                              <option value="{{ $key }}" {{ request($id) === $key ? 'selected' : '' }}>
                                    {{ $value }}
                              </option>
                        @endforeach
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                  </div>
            @else
                  <input type="{{ $type }}" id="{{ $id }}" name="{{ $id }}"
                        value="{{ request($id) }}" placeholder="Entrez {{ strtolower($label) }}..."
                        class="{{ $icon ? 'pl-10' : '' }} w-full rounded-lg border border-gray-300 dark:border-gray-600
                       bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-600 focus:border-indigo-600 p-2.5 outline-none">
            @endif
      </div>
</div>
