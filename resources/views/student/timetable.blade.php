@extends('layouts.app')

@section('content')
      <div class="p-6 md:p-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="mb-8 flex flex-col sm:flex-row items-center justify-between">
                  <h2 class="uppercase font-extrabold text-3xl text-gray-800 dark:text-gray-100">
                        Liste des programmes
                  </h2>
                  <nav class="mt-2 sm:mt-0">
                        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-300">
                              <li>
                                    <span class="font-medium text-violet-600"><i class="fa-solid fa-clock"></i></span>
                              </li>
                              <li>
                                    /<a class="font-medium hover:text-violet-600 transition-colors duration-300"
                                          href="{{ url('student/dashboard') }}">Dashboard</a>
                              </li>
                        </ol>
                  </nav>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                  @foreach ($getStudentTimetable as $studentTimetable)
                        <div
                              class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border border-gray-200 dark:border-gray-700">
                              <div class="relative p-6">
                                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500 to-indigo-500 opacity-10">
                                    </div>
                                    <div
                                          class="relative z-10 font-bold text-lg text-center text-gray-800 dark:text-gray-200 py-3 rounded-lg border border-violet-300 dark:border-violet-700 bg-violet-50 dark:bg-gray-700">
                                          <i class="fa-solid fa-book mr-2 text-violet-600"></i>
                                          {{ $studentTimetable['name'] }}
                                    </div>
                              </div>
                              <div class="relative overflow-x-auto p-4">
                                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                                          <thead
                                                class="text-xs uppercase text-white bg-gradient-to-r from-violet-600 to-indigo-600 rounded-t-xl">
                                                <tr>
                                                      <th scope="col" class="px-4 py-3">Jour</th>
                                                      <th scope="col" class="px-4 py-3">Heures</th>
                                                      <th scope="col" class="px-4 py-3 text-center">Salle</th>
                                                </tr>
                                          </thead>
                                          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @forelse($studentTimetable['week'] as $studentTime)
                                                      <tr
                                                            class="bg-white dark:bg-gray-800 hover:bg-violet-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                                            <td
                                                                  class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                                                  {{ $studentTime['week_name'] }}</td>
                                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                                                  @if ($studentTime['start_time'] && $studentTime['end_time'])
                                                                        {{ \Carbon\Carbon::parse($studentTime['start_time'])->format('H:i') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($studentTime['end_time'])->format('H:i') }}
                                                                  @else
                                                                        -
                                                                  @endif
                                                            </td>
                                                            <td
                                                                  class="px-4 py-3 text-center font-bold text-gray-700 dark:text-gray-300">
                                                                  {{ $studentTime['room_number'] ?: '-' }}</td>
                                                      </tr>
                                                @empty
                                                      <tr class="bg-white dark:bg-gray-800">
                                                            <td colspan="3"
                                                                  class="p-6 text-center text-gray-500 dark:text-gray-400 italic">
                                                                  Aucun horaire défini pour ce programme.
                                                            </td>
                                                      </tr>
                                                @endforelse
                                          </tbody>
                                    </table>
                              </div>
                        </div>
                  @endforeach
            </div>
      </div>
@endsection
