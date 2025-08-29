@php
      $user = Auth::user();
      $userType = $user->user_type;

      // Logo
      $setting = \App\Models\SettingModel::getSingle(1);
      $logo_url = !empty($setting->logo)
          ? \App\Models\SettingModel::getFaviconLogo($setting->logo)
          : asset('upload/logo.png');

      // Liens des dashboards par rôle
      $dashboardLinks = [
          1 => url('admin/dashboard'),
          2 => url('teacher/dashboard'),
          3 => url('student/dashboard'),
          4 => url('parent/dashboard'),
      ];
      $dashboardLink = $dashboardLinks[$userType] ?? url('login');

      // Libellés des rôles
      $roleLabels = [
          1 => 'Administrateur',
          2 => 'Professeur',
          3 => 'Apprenant',
          4 => 'Parent',
      ];
      $roleLabel = $roleLabels[$userType] ?? 'Utilisateur';

      // Menus par rôle
      $roleMenus = [
          1 => [
              ['url' => 'admin/account', 'icon' => 'user', 'label' => 'Mon profil'],
              ['url' => 'admin/change_password', 'icon' => 'lock', 'label' => 'Mon mot de passe'],
              ['url' => 'admin/settings', 'icon' => 'gear', 'label' => 'Paramètres'],
          ],
          2 => [
              ['url' => 'teacher/account', 'icon' => 'user', 'label' => 'Mon profil'],
              ['url' => 'teacher/change_password', 'icon' => 'lock', 'label' => 'Mon mot de passe'],
          ],
          3 => [
              ['url' => 'student/account', 'icon' => 'user', 'label' => 'Mon profil'],
              ['url' => 'student/change_password', 'icon' => 'lock', 'label' => 'Mon mot de passe'],
          ],
          4 => [
              ['url' => 'parent/account', 'icon' => 'user', 'label' => 'Mon profil'],
              ['url' => 'parent/change_password', 'icon' => 'lock', 'label' => 'Mon mot de passe'],
          ],
      ];
      $menus = $roleMenus[$userType] ?? [];

      // Photo de profil
      $profilePicture = !empty($user->profile_picture)
          ? 'upload/profile/' . $user->profile_picture
          : 'upload/default.jpg';
@endphp


<header
      class="sticky top-0 z-999 flex w-full border-gray-200 xl:border-b dark:border-gray-700 bg-white dark:bg-gray-900">
      <div class="flex flex-grow items-center justify-between xl:flex-row xl:px-6 xl:py-3 px-5">

            <!-- Hamburger + Logo -->
            <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
                  <button
                        class="z-99999 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:hidden"
                        @click.stop="sidebarToggle = !sidebarToggle">
                        <!-- (icône burger inchangée) -->
                  </button>
                  <a class="block flex-shrink-0 lg:hidden w-48" href="{{ $dashboardLink }}">
                        <img src="{{ $logo_url }}" alt="Logo" />
                  </a>
            </div>

            <!-- Barre de recherche -->
            <div class="hidden md:block">
                  <form action="" method="get">
                        <div class="relative w-full">
                              <button class="absolute left-0 top-1/2 -translate-y-1/2">
                                    <span
                                          class="absolute top-[50%] left-0 inline-flex -translate-y-1/2 items-center px-4 py-1 text-xs text-gray-500 dark:text-gray-400">
                                          <i class="fa-solid fa-search"></i>
                                    </span>
                              </button>
                              <input type="text" x-model="search" placeholder="Rechercher..."
                                    class="appearance-none w-full xl:w-[430px] rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-violet-600 focus:border-violet-600 py-2.5 pr-14 pl-12 outline-none" />
                        </div>
                  </form>
            </div>

            <!-- Zone Utilisateur -->
            <div class="flex items-center gap-3 2xsm:gap-7">

                  <ul class="flex items-center gap-2 2xsm:gap-4">
                        <li>
                              <!-- Dark Mode Toggler -->
                              <label :class="darkMode ? 'bg-gray-700' : 'bg-stroke'"
                                    class="relative m-0 block h-7.5 w-14 rounded-full">
                                    <input type="checkbox" :value="darkMode" @change="darkMode = !darkMode"
                                          class="absolute top-0 z-50 m-0 h-full w-full cursor-pointer opacity-0" />
                                    <span :class="darkMode && '!right-1 !translate-x-full'"
                                          class="absolute left-1 top-1/2 flex h-6 w-6 -translate-y-1/2 translate-x-0 items-center justify-center rounded-full bg-white shadow-switcher duration-75 ease-linear">
                                          <span class="dark:hidden">
                                                <i class="fa-solid fa-sun"></i>
                                          </span>
                                          <span class="hidden dark:inline-block">
                                                <i class="fa-solid fa-moon"></i>
                                          </span>
                                    </span>
                              </label>
                              <!-- Dark Mode Toggler -->
                        </li>

                        <!-- Notification Menu Area -->
                        <li class="relative" x-data="{ dropdownOpen: false, notifying: true }" @click.outside="dropdownOpen = false">
                              <a class="relative flex h-8.5 w-8.5 items-center justify-center rounded-full border-[0.5px] border-stroke bg-gray hover:text-primary dark:border-strokedark dark:bg-meta-4 dark:text-white"
                                    href="#" @click.prevent="dropdownOpen = ! dropdownOpen; notifying = false">
                                    <span :class="!notifying && 'hidden'"
                                          class="absolute -top-0.5 right-0 z-1 h-2 w-2 rounded-full bg-meta-1">
                                          <span
                                                class="absolute -z-1 inline-flex h-full w-full animate-ping rounded-full bg-meta-1 opacity-75"></span>
                                    </span>
                                    <iconify-icon icon="mdi:bell" width="18" height="18"></iconify-icon>
                              </a>

                              <!-- Dropdown Start -->
                              <div x-show="dropdownOpen" x-cloak
                                    class="absolute -right-27 mt-6 flex h-90 w-75 flex-col rounded-xl border border-stroke bg-white shadow-xl dark:border-strokedark dark:bg-boxdark sm:right-0 sm:w-80">
                                    <div class="px-4.5 py-3">
                                          <h5 class="text-sm font-medium text-bodydark2">Notifications</h5>
                                    </div>

                                    <ul class="flex h-auto flex-col overflow-y-auto">
                                          <li>
                                                <a class="flex flex-col gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4"
                                                      href="#">
                                                      <p class="text-sm">
                                                            <span class="text-black dark:text-white">Edit your
                                                                  information in a swipe</span>
                                                            Sint occaecat cupidatat non proident, sunt in culpa qui
                                                            officia deserunt mollit anim.
                                                      </p>

                                                      <p class="text-xs">12 May, 2025</p>
                                                </a>
                                          </li>
                                          <li>
                                                <a class="flex flex-col gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4"
                                                      href="#">
                                                      <p class="text-sm">
                                                            <span class="text-black dark:text-white">It is a long
                                                                  established fact</span>
                                                            that a reader will be distracted by the readable.
                                                      </p>

                                                      <p class="text-xs">24 Feb, 2025</p>
                                                </a>
                                          </li>
                                          <li>
                                                <a class="flex flex-col gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4"
                                                      href="#">
                                                      <p class="text-sm">
                                                            <span class="text-black dark:text-white">There are many
                                                                  variations</span>
                                                            of passages of Lorem Ipsum available, but the majority have
                                                            suffered
                                                      </p>

                                                      <p class="text-xs">04 Jan, 2025</p>
                                                </a>
                                          </li>
                                          <li>
                                                <a class="flex flex-col gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4"
                                                      href="#">
                                                      <p class="text-sm">
                                                            <span class="text-black dark:text-white">There are many
                                                                  variations</span>
                                                            of passages of Lorem Ipsum available, but the majority have
                                                            suffered
                                                      </p>

                                                      <p class="text-xs">01 Dec, 2024</p>
                                                </a>
                                          </li>
                                    </ul>
                              </div>
                              <!-- Dropdown End -->
                        </li>
                        <!-- Notification Menu Area -->

                        <!-- Chat Notification Area -->
                        <li class="relative" x-data="{ dropdownOpen: false, notifying: true }" @click.outside="dropdownOpen = false">
                              <a class="relative flex h-8.5 w-8.5 items-center justify-center rounded-full border-[0.5px] border-stroke bg-gray hover:text-primary dark:border-strokedark dark:bg-meta-4 dark:text-white"
                                    href="#" @click.prevent="dropdownOpen = ! dropdownOpen; notifying = false">
                                    <span :class="!notifying && 'hidden'"
                                          class="absolute -right-0.5 -top-0.5 z-1 h-2 w-2 rounded-full bg-meta-1">
                                          <span
                                                class="absolute -z-1 inline-flex h-full w-full animate-ping rounded-full bg-meta-1 opacity-75"></span>
                                    </span>
                                    <i class="fa-solid fa-comment" width="18" height="18"></i>
                              </a>

                              <!-- Dropdown Start -->
                              <div x-show="dropdownOpen" x-cloak
                                    class="absolute -right-16 mt-6 flex h-90 w-75 flex-col rounded-xl border border-stroke bg-white shadow-xl dark:border-strokedark dark:bg-boxdark sm:right-0 sm:w-80">
                                    <div class="px-4.5 py-3">
                                          <h5 class="text-sm font-medium text-bodydark2">Messages</h5>
                                    </div>

                                    <ul class="flex h-auto flex-col overflow-y-auto">
                                          <li>
                                                <a class="flex gap-4.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4"
                                                      href="">
                                                      <div class="h-12.5 w-12.5 rounded-full">
                                                            <img src="{{ asset('public/images/user/user-02.png') }}"
                                                                  alt="User" />
                                                      </div>

                                                      <div>
                                                            <h6 class="text-sm font-medium text-black dark:text-white">
                                                                  Mariya Desoja
                                                            </h6>
                                                            <p class="text-sm">I like your confidence 💪</p>
                                                            <p class="text-xs">2min ago</p>
                                                      </div>
                                                </a>
                                          </li>
                                          <li>
                                                <a class="flex gap-4.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4"
                                                      href="">
                                                      <div class="h-12.5 w-12.5 rounded-full">
                                                            <img src="{{ asset('public/images/user/user-01.png') }}"
                                                                  alt="User" />
                                                      </div>

                                                      <div>
                                                            <h6 class="text-sm font-medium text-black dark:text-white">
                                                                  Robert Jhon
                                                            </h6>
                                                            <p class="text-sm">Can you share your offer?</p>
                                                            <p class="text-xs">10min ago</p>
                                                      </div>
                                                </a>
                                          </li>
                                          <li>
                                                <a class="flex gap-4.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4"
                                                      href="">
                                                      <div class="h-12.5 w-12.5 rounded-full">
                                                            <img src="{{ asset('public/images/user/user-03.png') }}"
                                                                  alt="User" />
                                                      </div>

                                                      <div>
                                                            <h6 class="text-sm font-medium text-black dark:text-white">
                                                                  Henry Dholi
                                                            </h6>
                                                            <p class="text-sm">I cam across your profile and...</p>
                                                            <p class="text-xs">1day ago</p>
                                                      </div>
                                                </a>
                                          </li>
                                          <li>
                                                <a class="flex gap-4.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4"
                                                      href="">
                                                      <div class="h-12.5 w-12.5 rounded-full">
                                                            <img src="{{ asset('public/images/user/user-01.png') }}"
                                                                  alt="User" />
                                                      </div>

                                                      <div>
                                                            <h6 class="text-sm font-medium text-black dark:text-white">
                                                                  Cody Fisher
                                                            </h6>
                                                            <p class="text-sm">I’m waiting for you response!</p>
                                                            <p class="text-xs">5days ago</p>
                                                      </div>
                                                </a>
                                          </li>
                                          <li>
                                                <a class="flex gap-4.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 dark:border-strokedark dark:hover:bg-meta-4"
                                                      href="">
                                                      <div class="h-12.5 w-12.5 rounded-full">
                                                            <img src=".{{ asset('public/images/user/user-02.png') }}"
                                                                  alt="User" />
                                                      </div>

                                                      <div>
                                                            <h6 class="text-sm font-medium text-black dark:text-white">
                                                                  Mariya Desoja
                                                            </h6>
                                                            <p class="text-sm">I like your confidence 💪</p>
                                                            <p class="text-xs">2min ago</p>
                                                      </div>
                                                </a>
                                          </li>
                                    </ul>
                              </div>
                              <!-- Dropdown End -->
                        </li>
                        <!-- Chat Notification Area -->
                  </ul>

                  <!-- Dark mode / Notifications / Messages (inchangés pour éviter de gonfler le code) -->

                  <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
                        <a class="flex items-center gap-4" href="#"
                              @click.prevent="dropdownOpen = ! dropdownOpen">
                              <span class="hidden text-right lg:block">
                                    <span class="block text-sm font-medium text-black dark:text-white">
                                          {{ $user->name }} {{ $user->last_name }}
                                    </span>
                                    <span class="block text-xs font-medium">{{ $roleLabel }}</span>
                              </span>
                              <span class="h-12 w-12 rounded-full">
                                    <img src="{{ asset($profilePicture) }}" alt="{{ $user->name }}"
                                          class="h-12 w-12 rounded-full object-cover object-center" />
                              </span>
                              <span :class="dropdownOpen && 'rotate-180'" class="hidden fill-current sm:block">
                                    <i class="fa-solid fa-chevron-down"></i>
                              </span>
                        </a>

                        <!-- Dropdown -->
                        <div x-show="dropdownOpen" x-cloak
                              class="absolute right-0 mt-5 flex w-72 flex-col rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                              <ul
                                    class="flex flex-col gap-x-2 border-b border-stroke dark:border-strokedark px-4 py-1.5">
                                    @foreach ($menus as $menu)
                                          <li>
                                                <a href="{{ url($menu['url']) }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 px-3 py-1.5 rounded-md ease-in-out dark:hover:bg-gray-600 dark:text-gray-300 text-gray-700 hover:bg-indigo-500/25 hover:text-indigo-600 lg:text-base">
                                                      <span><i class="fa-solid fa-{{ $menu['icon'] }}"></i></span>
                                                      {{ $menu['label'] }}
                                                </a>
                                          </li>
                                    @endforeach
                              </ul>
                              <a href="{{ url('logout') }}"
                                    class="flex items-center gap-3.5 px-6 py-4 text-sm font-medium duration-300 ease-in-out dark:hover:bg-gray-600 dark:text-gray-300 text-gray-700 hover:bg-indigo-500/25 hover:text-indigo-600 lg:text-base">
                                    <span class="text-[22px]">
                                          <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    </span>
                                    Déconnexion
                              </a>
                        </div>
                  </div>
            </div>
      </div>
</header>
