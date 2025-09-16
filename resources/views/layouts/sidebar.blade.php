<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
      class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-700 dark:bg-gray-900 lg:static lg:translate-x-0">

      <!-- HEADER -->
      @php
            $setting = \App\Models\SettingModel::getSingle(1);
            $logo_url = !empty($setting->logo)
                ? \App\Models\SettingModel::getFaviconLogo($setting->logo)
                : asset('upload/logo.png');

            $role = Auth::user()->user_type; // 1=Admin, 2=Teacher, 3=Student, 4=Parent

            $homeLinks = [
                1 => url('admin/dashboard'),
                2 => url('teacher/dashboard'),
                3 => url('student/dashboard'),
                4 => url('parent/dashboard'),
            ];

            $homeLink = $homeLinks[$role] ?? url('login');
      @endphp

      <div class="flex items-center justify-between lg:justify-center gap- py-2">
            <a href="{{ $homeLink }}">
                  <img src="{{ $logo_url }}" alt="Logo" class="w-48 h-24 object-cover rounded-lg ms-6 sm:m-1" />
            </a>
            <button class="block lg:hidden" @click.stop="sidebarToggle = !sidebarToggle">
                  <i class="fa-solid fa-arrow-left"></i>
            </button>
      </div>

      <!-- MENU -->
      <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
            <nav class="mb-8" x-data="{ selected: $persist('dashboard') }">

                  <!-- NOM UTILISATEUR -->
                  <h3 class="mb-3 ml-4 text-md font-medium text-bodydark2 dark:text-white">
                        <span class="self-center font-bold whitespace-nowrap">
                              {{ Auth::user()->name }} {{ Auth::user()->last_name }}
                        </span>
                  </h3>

                  <ul class="mb-6 flex flex-col gap-1.5">

                        @php
                              // ✅ Définition des menus selon le rôle
                              $menus = [
                                  1 => [
                                      // ----- ADMIN -----
                                      'items' => [
                                          [
                                              'url' => 'admin/dashboard',
                                              'icon' => 'fa-house-chimney',
                                              'label' => 'Dashboard',
                                              'segment' => 2,
                                              'match' => 'dashboard',
                                          ],
                                          [
                                              'url' => 'admin/admin/list',
                                              'icon' => 'fa-user-shield',
                                              'label' => 'Administrateurs',
                                              'segment' => 2,
                                              'match' => 'admin',
                                          ],
                                          [
                                              'url' => 'admin/teacher/list',
                                              'icon' => 'fa-chalkboard-teacher',
                                              'label' => 'Professeurs',
                                              'segment' => 2,
                                              'match' => 'teacher',
                                          ],
                                          [
                                              'url' => 'admin/student/list',
                                              'icon' => 'fa-user-graduate',
                                              'label' => 'Apprenants',
                                              'segment' => 2,
                                              'match' => 'student',
                                          ],
                                          [
                                              'url' => 'admin/parent/list',
                                              'icon' => 'fa-user-group',
                                              'label' => 'Parents',
                                              'segment' => 2,
                                              'match' => 'parent',
                                          ],
                                          [
                                              'url' => 'admin/class/list',
                                              'icon' => 'fa-landmark',
                                              'label' => 'Classes',
                                              'segment' => 2,
                                              'match' => 'class',
                                          ],
                                          [
                                              'url' => 'admin/subject/list',
                                              'icon' => 'fa-book-open-reader',
                                              'label' => 'Matières',
                                              'segment' => 2,
                                              'match' => 'subject',
                                          ],
                                      ],
                                      'dropdowns' => [
                                          'assign' => [
                                              'icon' => 'fa-arrows-spin',
                                              'label' => 'Assignations',
                                              'items' => [
                                                  [
                                                      'url' => 'admin/assign_class/list',
                                                      'label' => 'Classes',
                                                      'segment' => 2,
                                                      'match' => 'assign_class',
                                                  ],
                                                  [
                                                      'url' => 'admin/assign_subject/list',
                                                      'label' => 'Matières',
                                                      'segment' => 2,
                                                      'match' => 'assign_subject',
                                                  ],
                                                  [
                                                      'url' => 'admin/class_timetable/list',
                                                      'label' => 'Horaires',
                                                      'segment' => 2,
                                                      'match' => 'class_timetable',
                                                  ],
                                              ],
                                          ],
                                          'examinations' => [
                                              'icon' => 'fa-flask-vial',
                                              'label' => 'Evaluations',
                                              'items' => [
                                                  [
                                                      'url' => 'admin/examinations/exam/list',
                                                      'label' => 'Examens',
                                                      'segment' => 3,
                                                      'match' => 'exam',
                                                  ],
                                                  [
                                                      'url' => 'admin/examinations/schedule/list',
                                                      'label' => 'Programmations',
                                                      'segment' => 3,
                                                      'match' => 'schedule',
                                                  ],
                                                  [
                                                      'url' => 'admin/examinations/marks_register/list',
                                                      'label' => 'Registres',
                                                      'segment' => 3,
                                                      'match' => 'marks_register',
                                                  ],
                                                  [
                                                      'url' => 'admin/examinations/marks_grade/list',
                                                      'label' => 'Notes',
                                                      'segment' => 3,
                                                      'match' => 'marks_grade',
                                                  ],
                                              ],
                                          ],
                                          'attendance' => [
                                              'icon' => 'mdi:user-check',
                                              'label' => 'Présence',
                                              'items' => [
                                                  [
                                                      'url' => 'admin/attendance/students/list',
                                                      'label' => 'Apprenants',
                                                      'segment' => 3,
                                                      'match' => 'students',
                                                  ],
                                                  [
                                                      'url' => 'admin/attendance/report',
                                                      'label' => 'Rapports',
                                                      'segment' => 3,
                                                      'match' => 'report',
                                                  ],
                                              ],
                                          ],
                                          'communicate' => [
                                              'icon' => 'mdi:bell',
                                              'label' => 'Notifications',
                                              'items' => [
                                                  [
                                                      'url' => 'admin/communicate/noticeboard/list',
                                                      'label' => 'Listes',
                                                      'segment' => 3,
                                                      'match' => 'noticeboard',
                                                  ],
                                                  [
                                                      'url' => 'admin/communicate/send_mail',
                                                      'label' => 'Mails',
                                                      'segment' => 3,
                                                      'match' => 'send_mail',
                                                  ],
                                              ],
                                          ],
                                          'practicalworks' => [
                                              'icon' => 'mdi:home-edit',
                                              'label' => 'Devoirs',
                                              'items' => [
                                                  [
                                                      'url' => 'admin/practicalworks/homework/list',
                                                      'label' => 'Travaux',
                                                      'segment' => 3,
                                                      'match' => 'homework',
                                                  ],
                                                  [
                                                      'url' => 'admin/practicalworks/reports',
                                                      'label' => 'Rapports',
                                                      'segment' => 3,
                                                      'match' => 'reports',
                                                  ],
                                              ],
                                          ],
                                          'feescollections' => [
                                              'icon' => 'mdi:cash-register',
                                              'label' => 'Contributions',
                                              'items' => [
                                                  [
                                                      'url' => 'admin/feescollections/collections/list',
                                                      'label' => 'Collecter',
                                                      'segment' => 3,
                                                      'match' => 'collections',
                                                  ],
                                                  [
                                                      'url' => 'admin/feescollections/feescollects/feesList',
                                                      'label' => 'Rapports',
                                                      'segment' => 3,
                                                      'match' => 'feescollects',
                                                  ],
                                              ],
                                          ],
                                          'settings' => [
                                              'icon' => 'mdi:settings',
                                              'label' => 'Paramètres',
                                              'items' => [
                                                  [
                                                      'url' => 'admin/settings',
                                                      'label' => 'Menus',
                                                      'segment' => 2,
                                                      'match' => 'settings',
                                                  ],
                                                  [
                                                      'url' => 'admin/test',
                                                      'label' => 'Test',
                                                      'segment' => 2,
                                                      'match' => 'test',
                                                  ],
                                              ],
                                          ],
                                      ],
                                  ],

                                  2 => [
                                      // ----- TEACHER -----
                                      'items' => [
                                          [
                                              'url' => 'teacher/dashboard',
                                              'icon' => 'fa-house-chimney',
                                              'label' => 'Dashboard',
                                              'segment' => 2,
                                              'match' => 'dashboard',
                                          ],
                                          [
                                              'url' => 'teacher/account',
                                              'icon' => 'fa-circle-user',
                                              'label' => 'Mon Profile',
                                              'segment' => 2,
                                              'match' => 'account',
                                          ],
                                          [
                                              'url' => 'teacher/my_student',
                                              'icon' => 'fa-user-graduate',
                                              'label' => 'Mes Apprenants',
                                              'segment' => 2,
                                              'match' => 'my_student',
                                          ],
                                          [
                                              'url' => 'teacher/class_subject',
                                              'icon' => 'fa-landmark',
                                              'label' => 'Matières & Classes',
                                              'segment' => 2,
                                              'match' => 'class_subject',
                                          ],
                                          [
                                              'url' => 'teacher/my_exam_timetable',
                                              'icon' => 'fa-flask-vial',
                                              'label' => 'Evaluations',
                                              'segment' => 2,
                                              'match' => 'my_exam_timetable',
                                          ],
                                          [
                                              'url' => 'teacher/my_calendar',
                                              'icon' => 'fa-calendar-days',
                                              'label' => 'Mon Calendrier',
                                              'segment' => 2,
                                              'match' => 'my_calendar',
                                          ],
                                          [
                                              'url' => 'teacher/marks_register',
                                              'icon' => 'fa-registered',
                                              'label' => 'Mon Registre',
                                              'segment' => 2,
                                              'match' => 'marks_register',
                                          ],
                                          [
                                              'url' => 'teacher/my_noticeboard',
                                              'icon' => 'fa-bell',
                                              'label' => 'Mes Notifications',
                                              'segment' => 2,
                                              'match' => 'my_noticeboard',
                                          ],
                                      ],
                                      'dropdowns' => [
                                          'attendance' => [
                                              'icon' => 'mdi:user-check',
                                              'label' => 'Présence',
                                              'items' => [
                                                  [
                                                      'url' => 'teacher/attendance/student/list',
                                                      'label' => 'Apprenants',
                                                      'segment' => 3,
                                                      'match' => 'student',
                                                  ],
                                                  [
                                                      'url' => 'teacher/attendance/report',
                                                      'label' => 'Rapports',
                                                      'segment' => 3,
                                                      'match' => 'report',
                                                  ],
                                              ],
                                          ],
                                          'practicalworks' => [
                                              'icon' => 'mdi:home-edit',
                                              'label' => 'Devoirs',
                                              'items' => [
                                                  [
                                                      'url' => 'teacher/practicalworks/homework/list',
                                                      'label' => 'Travaux Maison',
                                                      'segment' => 3,
                                                      'match' => 'homework',
                                                  ],
                                              ],
                                          ],
                                      ],
                                  ],

                                  3 => [
                                      // ----- STUDENT -----
                                      'items' => [
                                          [
                                              'url' => 'student/dashboard',
                                              'icon' => 'fa-house-chimney',
                                              'label' => 'Dashboard',
                                              'segment' => 2,
                                              'match' => 'dashboard',
                                          ],
                                          [
                                              'url' => 'student/account',
                                              'icon' => 'fa-user-circle',
                                              'label' => 'Mon Profile',
                                              'segment' => 2,
                                              'match' => 'account',
                                          ],
                                          [
                                              'url' => 'student/my_calendar',
                                              'icon' => 'fa-calendar-days',
                                              'label' => 'Mon Calendrier',
                                              'segment' => 2,
                                              'match' => 'my_calendar',
                                          ],
                                          [
                                              'url' => 'student/my_subject',
                                              'icon' => 'fa-book',
                                              'label' => 'Mes Matières',
                                              'segment' => 2,
                                              'match' => 'my_subject',
                                          ],
                                          [
                                              'url' => 'student/my_timetable',
                                              'icon' => 'fa-clock',
                                              'label' => 'Mes Programmations',
                                              'segment' => 2,
                                              'match' => 'my_timetable',
                                          ],
                                          [
                                              'url' => 'student/my_exam_timetable',
                                              'icon' => 'fa-flask-vial',
                                              'label' => 'Mes Evaluations',
                                              'segment' => 2,
                                              'match' => 'my_exam_timetable',
                                          ],
                                          [
                                              'url' => 'student/my_exam_result',
                                              'icon' => 'fa-square-poll-horizontal',
                                              'label' => 'Mes Résultats',
                                              'segment' => 2,
                                              'match' => 'my_exam_result',
                                          ],
                                          [
                                              'url' => 'student/my_attendance',
                                              'icon' => 'fa-user-check',
                                              'label' => 'Ma Présence',
                                              'segment' => 2,
                                              'match' => 'my_attendance',
                                          ],
                                          [
                                              'url' => 'student/my_noticeboard',
                                              'icon' => 'mdi:bell',
                                              'label' => 'Mes Notifications',
                                              'segment' => 2,
                                              'match' => 'my_noticeboard',
                                          ],
                                          [
                                              'url' => 'student/my_homework',
                                              'icon' => 'mdi:home-edit',
                                              'label' => 'Mes Devoirs',
                                              'segment' => 2,
                                              'match' => 'my_homework',
                                          ],
                                          [
                                              'url' => 'student/my_fees',
                                              'icon' => 'fa-cash-register',
                                              'label' => 'Mes Contributions',
                                              'segment' => 2,
                                              'match' => 'my_fees',
                                          ],
                                      ],
                                  ],

                                  4 => [
                                      // ----- PARENT -----
                                      'items' => [
                                          [
                                              'url' => 'parent/dashboard',
                                              'icon' => 'fa-house-chimney',
                                              'label' => 'Dashboard',
                                              'segment' => 2,
                                              'match' => 'dashboard',
                                          ],
                                          [
                                              'url' => 'parent/account',
                                              'icon' => 'fa-user-circle',
                                              'label' => 'Mon Profile',
                                              'segment' => 2,
                                              'match' => 'account',
                                          ],
                                          [
                                              'url' => 'parent/my_student',
                                              'icon' => 'fa-user-graduate',
                                              'label' => 'Mes Enfants',
                                              'segment' => 2,
                                              'match' => 'my_student',
                                          ],
                                          [
                                              'url' => 'parent/my_noticeboard',
                                              'icon' => 'mdi:bell',
                                              'label' => 'Mes Notifications',
                                              'segment' => 2,
                                              'match' => 'my_noticeboard',
                                          ],
                                      ],
                                  ],
                              ];

                              $roleMenus = $menus[$role] ?? [];
                        @endphp

                        {{-- Boucle sur les menus simples --}}
                        @foreach ($roleMenus['items'] ?? [] as $menu)
                              <li>
                                    <a href="{{ url($menu['url']) }}"
                                          class="group flex items-center gap-3 px-4 py-1.5 rounded-t-lg font-medium transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300
                                            {{ Request::segment($menu['segment']) === $menu['match']
                                                ? 'bg-indigo-500/25 hover:shadow-xl text-indigo-600 dark:bg-gray-700'
                                                : 'hover:bg-indigo-500/25 hover:shadow-xl hover:text-indigo-600 dark:hover:bg-gray-400/25 dark:hover:text-gray-300 dark:hover:shadow-xl' }}">
                                          <span class="w-5 h-5 p-4 flex items-center justify-center">
                                                @php
                                                      $icon = $menu['icon'];
                                                      $isFa = str_contains($icon, 'fa');
                                                      $isMdi = str_contains($icon, 'mdi:');
                                                @endphp

                                                @if ($isFa)
                                                      <i class="fa {{ $icon }}"></i>
                                                @elseif ($isMdi)
                                                      <iconify-icon icon="{{ $icon }}" width="20"
                                                            height="20"></iconify-icon>
                                                @else
                                                      {{-- Fallback Iconify if Font Awesome not found --}}
                                                      <iconify-icon icon="{{ $icon }}" width="20"
                                                            height="20"></iconify-icon>
                                                @endif
                                          </span>


                                          <span class="">{{ $menu['label'] }}</span>
                                    </a>
                              </li>
                        @endforeach

                        {{-- Boucle sur les dropdowns --}}
                        @php
                              $activeKey = '';
                              foreach ($roleMenus['dropdowns'] ?? [] as $key => $drop) {
                                  foreach ($drop['items'] as $item) {
                                      if (Request::segment($item['segment']) === $item['match']) {
                                          $activeKey = $key;
                                          break 2;
                                      }
                                  }
                              }
                        @endphp

                        <ul x-data="{ selectedKey: '{{ $activeKey }}' }">
                              @foreach ($roleMenus['dropdowns'] ?? [] as $key => $drop)
                                    @php
                                          $isActive = collect($drop['items'])->contains(function ($item) {
                                              return Request::segment($item['segment']) === $item['match'];
                                          });
                                    @endphp

                                    <li>
                                          <a href="#"
                                                @click.prevent="selectedKey = selectedKey === '{{ $key }}' ? null : '{{ $key }}'"
                                                class="group relative flex items-center gap-3 px-4 py-2 rounded-t-lg font-medium transition-all duration-300 ease-in-out
                                                text-gray-700 dark:text-gray-300 hover:bg-indigo-500/25 hover:shadow-xl hover:text-indigo-600 dark:hover:bg-gray-400/25 dark:hover:text-gray-300 dark:hover:shadow-xl"
                                                :class="{ 'bg-indigo-500/25 hover:shadow-xl text-indigo-600 dark:bg-gray-700': selectedKey === '{{ $key }}' }">

                                                <span class="w-5 h-5 p-4 flex items-center justify-center">
                                                      @php
                                                            $icon = $drop['icon'];
                                                            $isMdi = str_contains($icon, 'mdi:');
                                                            $isFa = str_contains($icon, 'fa');
                                                      @endphp

                                                      @if ($isFa)
                                                            <i class="fa {{ $icon }}"></i>
                                                      @elseif ($isMdi)
                                                            <iconify-icon icon="{{ $icon }}" width="20"
                                                                  height="20"></iconify-icon>
                                                      @else
                                                            {{-- Fallback Iconify if Font Awesome not found --}}
                                                            <iconify-icon icon="{{ $icon }}" width="20"
                                                                  height="20"></iconify-icon>
                                                      @endif
                                                </span>


                                                <span class="">{{ $drop['label'] }}</span>

                                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 transition duration-300"
                                                      :class="{ 'rotate-180': selectedKey === '{{ $key }}' }"></i>
                                          </a>

                                          <div x-show="selectedKey === '{{ $key }}'"
                                                x-transition:enter="transition-all ease-out duration-700"
                                                x-transition:enter-start="opacity-0 max-h-0"
                                                x-transition:enter-end="opacity-100 max-h-[1000px]"
                                                x-transition:leave="transition-all ease-in-out duration-700"
                                                x-transition:leave-start="opacity-100 max-h-[1000px]"
                                                x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">

                                                <ul class="mt-4 flex flex-col gap-2.5 pl-5">
                                                      @foreach ($drop['items'] as $item)
                                                            <li>
                                                                  <a href="{{ url($item['url']) }}"
                                                                        class="group flex items-center gap-3 px-4 py-2 rounded-t-lg font-medium transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300
                                                                                    {{ Request::segment($item['segment']) === $item['match']
                                                                                        ? 'bg-indigo-500/25 hover:shadow-xl text-indigo-600 dark:bg-gray-700'
                                                                                        : 'hover:bg-indigo-500/25 hover:shadow-xl hover:text-indigo-700 dark:hover:bg-gray-400/25 dark:hover:text-gray-300 dark:hover:shadow-xl' }}">
                                                                        <span
                                                                              class="w-5 h-5 flex items-center justify-center">
                                                                              <i class="fa-solid fa-circle-dot"></i>
                                                                        </span>

                                                                        <span
                                                                              class="">{{ $item['label'] }}</span>
                                                                  </a>
                                                            </li>
                                                      @endforeach
                                                </ul>
                                          </div>
                                    </li>
                              @endforeach
                        </ul>

                        <!-- Déconnexion -->
                        <li class="absolute bottom-0 left-0 px-4 py-2.5 w-full">
                              <div onclick="window.location.href='{{ url('logout') }}'"
                                    class="group cursor-pointer flex items-center px-4 py-1 rounded-lg transition-all duration-300 ease-in-out
                                    hover:bg-indigo-500/25 hover:shadow-xl hover:text-indigo-600 text-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                                    <span class="w-5 h-5 p-4 flex items-center justify-center">
                                          <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    </span>
                                    <span class="ml-3">Déconnexion</span>
                              </div>
                        </li>
                  </ul>
            </nav>
      </div>
</aside>
