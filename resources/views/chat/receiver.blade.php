  @php
        use Carbon\Carbon;

        $profilePicture = !empty($getReceiver->profile_picture)
            ? 'upload/profile/' . $getReceiver->profile_picture
            : 'upload/default.jpg';

        $isOnline =
            $getReceiver->last_login &&
            Carbon::parse($getReceiver->last_login)->greaterThan(Carbon::now()->subMinutes(5));
  @endphp

  <div class="flex mb-4 cursor-pointer">
        <div class="w-9 h-9 rounded-full flex items-center justify-center mr-2">
              <img src="{{ $profilePicture }}" alt="User Avatar"
                    class="w-8 h-8 rounded-full">
        </div>
        <div class="flex max-w-96 bg-gray-300 rounded-lg p-3 gap-3">
              <p class="text-gray-700">{{ $getReceiver->last_name }} {{ $getReceiver->name }}</p>
        </div>
  </div>
