<aside :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
      class="absolute left-0 top-0 z-9999 flex h-screen w-72.5 flex-col overflow-y-hidden bg-white duration-300 ease-linear dark:bg-boxdark lg:static lg:translate-x-0"
      @click.outside="sidebarToggle = false">

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

      <div class="flex items-center justify-between lg:justify-center gap-2">
            <a href="{{ $homeLink }}">
                  <img src="{{ $logo_url }}" alt="Logo" class="w-48 h-24" />
            </a>
            <button class="block lg:hidden" @click.stop="sidebarToggle = !sidebarToggle">
                  <i class="fa-solid fa-arrow-left"></i>
            </button>
      </div>

      <!-- MENU -->
      <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
            <nav class="p-2 lg:px-5 mb-8" x-data="{ selected: $persist('dashboard') }">

                  <!-- NOM UTILISATEUR -->
                  <h3 class="mb-4 ml-4 text-md font-medium text-bodydark2 dark:text-white">
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
                                                      'label' => 'Percevoir',
                                                      'segment' => 3,
                                                      'match' => 'collections',
                                                  ],
                                                  [
                                                      'url' => 'admin/feescollections/feescollects/feesList',
                                                      'label' => 'Reçues',
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
                                              'url' => 'teacher/my_student',
                                              'icon' => 'fa-user-graduate',
                                              'label' => 'Mes Étudiants',
                                              'segment' => 2,
                                              'match' => 'my_student',
                                          ],
                                          [
                                              'url' => 'teacher/my_class_subject',
                                              'icon' => 'fa-book',
                                              'label' => 'Matières & Classes',
                                              'segment' => 2,
                                              'match' => 'my_class_subject',
                                          ],
                                      ],
                                      'dropdowns' => [
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
                                                  [
                                                      'url' => 'teacher/practicalworks/reports',
                                                      'label' => 'Rapports',
                                                      'segment' => 3,
                                                      'match' => 'reports',
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
                                              'url' => 'student/my_subjects',
                                              'icon' => 'fa-book',
                                              'label' => 'Mes Matières',
                                              'segment' => 2,
                                              'match' => 'my_subjects',
                                          ],
                                      ],
                                      'dropdowns' => [
                                          'practicalworks' => [
                                              'icon' => 'mdi:home-edit',
                                              'label' => 'Devoirs',
                                              'items' => [
                                                  [
                                                      'url' => 'student/practicalworks/homework/list',
                                                      'label' => 'Travaux Maison',
                                                      'segment' => 3,
                                                      'match' => 'homework',
                                                  ],
                                                  [
                                                      'url' => 'student/practicalworks/reports',
                                                      'label' => 'Rapports',
                                                      'segment' => 3,
                                                      'match' => 'reports',
                                                  ],
                                              ],
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
                                              'url' => 'parent/my_children',
                                              'icon' => 'fa-user-group',
                                              'label' => 'Mes Enfants',
                                              'segment' => 2,
                                              'match' => 'my_children',
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
                                          class="group flex items-center gap-3 px-4 py-1.5 rounded-sm font-medium transition-all duration-700 ease-in-out
                                            {{ Request::segment($menu['segment']) === $menu['match']
                                                ? 'bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-600 hover:to-violet-500 hover:shadow-xl text-white'
                                                : 'text-violet-500 dark:text-white hover:bg-gradient-to-r hover:from-violet-600 hover:to-violet-600 dark:hover:bg-meta-4 hover:shadow-xl hover:translate-x-2' }}">
                                          <span
                                                class="w-5 h-5 p-4 flex items-center justify-center rounded-sm bg-violet-100 text-violet-600 group-hover:text-violet-600 group-hover:bg-violet-100 group-hover:shadow-xl">
                                                <i class="fa-solid {{ $menu['icon'] }}"></i>
                                          </span>

                                          <span
                                                class="group-hover:text-white dark:text-white">{{ $menu['label'] }}</span>
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
                                                class="group relative flex items-center gap-3 px-4 py-2 rounded-sm font-medium transition-all duration-300 ease-in-out
                                                              text-violet-600 dark:text-white hover:bg-gradient-to-r hover:from-violet-600 hover:to-violet-500 hover:text-white hover:shadow-xl hover:translate-x-2"
                                                :class="{ 'bg-gradient-to-r from-violet-600 to-violet-500 text-white shadow-xl': selectedKey === '{{ $key }}' }">

                                                <span
                                                      class="w-5 h-5 p-4 flex items-center justify-center rounded-sm bg-violet-100 text-violet-600 group-hover:text-violet-600 group-hover:bg-violet-100 group-hover:shadow-xl">
                                                      @if (str_contains($drop['icon'], 'mdi:'))
                                                            <iconify-icon icon="{{ $drop['icon'] }}" width="20"
                                                                  height="20"></iconify-icon>
                                                      @else
                                                            <i class="fa-solid {{ $drop['icon'] }}"></i>
                                                      @endif
                                                </span>

                                                <span
                                                      class="group-hover:text-white dark:text-white">{{ $drop['label'] }}</span>

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
                                                                        class="group flex items-center gap-3 px-4 py-2 rounded-sm font-medium transition-all duration-300 ease-in-out
                                                                                    {{ Request::segment($item['segment']) === $item['match']
                                                                                        ? 'bg-gradient-to-r from-violet-600 to-violet-500 text-white shadow-xl'
                                                                                        : 'text-violet-500 dark:text-white hover:bg-gradient-to-r hover:from-violet-600 hover:to-violet-600 hover:text-white hover:shadow-xl hover:translate-x-2' }}">
                                                                        <span
                                                                              class="w-5 h-5 flex items-center justify-center">
                                                                              <i class="fa-solid fa-chevron-right"></i>
                                                                        </span>

                                                                        <span
                                                                              class="group-hover:text-white dark:text-white">{{ $item['label'] }}</span>
                                                                  </a>
                                                            </li>
                                                      @endforeach
                                                </ul>
                                          </div>
                                    </li>
                              @endforeach
                        </ul>

                        <!-- Déconnexion -->
                        <li class="absolute bottom-2 left-0 px-4 py-2.5 w-full">
                              <div onclick="window.location.href='{{ url('logout') }}'"
                                    class="cursor-pointer flex items-center px-4 py-2.5 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-600 hover:to-violet-500 text-white hover:translate-x-2 transition-all duration-300 ease-in-out hover:shadow-xl">
                                    <span
                                          class="w-5 h-5 flex items-center justify-center rounded-sm bg-violet-100 text-violet-600">
                                          <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    </span>
                                    <span class="ml-3">Déconnexion</span>
                              </div>
                        </li>
                  </ul>
            </nav>
      </div>
</aside>
