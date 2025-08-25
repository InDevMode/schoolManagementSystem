@props(['id', 'name', 'label' => null, 'required' => false])

<div class="mb-6 w-full">
      @if ($label)
            <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $label }} @if ($required)
                        <span class="text-red-500">*</span>
                  @endif
            </label>
      @endif
      <input type="file" id="{{ $id }}" name="{{ $name }}"
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full cursor-pointer rounded-lg border-[1.5px] border-stroke bg-gray-50
                                file:mr-5 file:cursor-pointer file:border-0 file:px-5 file:py-3
                                file:hover:bg-violet-400 file:hover:bg-opacity-10
                                dark:border-gray-600 dark:bg-gray-700 dark:file:bg-white/30
                                dark:file:text-white focus:border-violet-400 active:border-violet-400',
            ]) }}>
</div>
