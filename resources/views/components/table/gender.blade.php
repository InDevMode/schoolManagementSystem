@props(['gender' => 'other'])

<span
      class="px-2 py-1 border w-24 inline-flex justify-center text-xs leading-5 font-semibold rounded-full
    {{ $gender === 'male'
        ? 'bg-indigo-100 border-indigo-800 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200'
        : ($gender === 'female'
            ? 'bg-red-100 border-red-800 text-red-800 dark:bg-red-900 dark:text-red-200'
            : 'bg-pink-100 border-pink-800 text-pink-800 dark:bg-pink-900 dark:text-pink-200') }}">
      {{ $gender === 'male' ? 'Masculin' : ($gender === 'female' ? 'Féminin' : 'Autre') }}
</span>
