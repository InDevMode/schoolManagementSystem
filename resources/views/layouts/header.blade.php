<header
      class="sticky top-0 z-999 flex w-full border-gray-200 xl:border-b dark:border-gray-800 bg-white dark:bg-gray-900">
      <div class="flex flex-grow items-center justify-between xl:flex-row xl:px-6 xl:py-3 px-5">
            <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
                  <!-- Hamburger Toggle BTN -->
                  <button
                        class="z-99999 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm dark:border-gray-800 dark:bg-gray-800 lg:hidden"
                        @click.stop="sidebarToggle = !sidebarToggle">
                        <span class="relative block h-5.5 w-5.5 cursor-pointer">
                              <span class="du-block absolute right-0 h-full w-full">
                                    <span class="relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-[0] duration-200 ease-in-out dark:bg-white"
                                          :class="{ '!w-full delay-300': !sidebarToggle }"></span>
                                    <span class="relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-150 duration-200 ease-in-out dark:bg-white"
                                          :class="{ '!w-full delay-400': !sidebarToggle }"></span>
                                    <span class="relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-200 duration-200 ease-in-out dark:bg-white"
                                          :class="{ '!w-full delay-500': !sidebarToggle }"></span>
                              </span>
                              <span class="du-block absolute right-0 h-full w-full rotate-45">
                                    <span class="absolute left-2.5 top-0 block h-full w-0.5 rounded-sm bg-black delay-300 duration-200 ease-in-out dark:bg-white"
                                          :class="{ '!h-0 delay-[0]': !sidebarToggle }"></span>
                                    <span class="delay-400 absolute left-0 top-2.5 block h-0.5 w-full rounded-sm bg-black duration-200 ease-in-out dark:bg-white"
                                          :class="{ '!h-0 dealy-200': !sidebarToggle }"></span>
                              </span>
                        </span>
                  </button>

                  @php
                        $getSettingFaviconAndLogo = \App\Models\SettingModel::getSingle(1);
                        $logo_url = !empty($getSettingFaviconAndLogo->logo)
                            ? \App\Models\SettingModel::getFaviconLogo($getSettingFaviconAndLogo->logo)
                            : asset('upload/logo.png');
                        $links = [
                            1 => url('admin/dashboard'), // Admin
                            2 => url('teacher/dashboard'), // Teacher
                            3 => url('student/dashboard'), // Student
                            4 => url('parent/dashboard'), // Parent
                        ];
                        $link = $links[Auth::user()->user_type] ?? url('login'); // Lien de secours

                  @endphp

                  <!-- Hamburger Toggle BTN -->
                  <a class="block flex-shrink-0 lg:hidden w-48" href="{{ $link }}">
                        <img src="{{ $logo_url }}" alt="Logo" />
                  </a>
            </div>
            <div class="hidden md:block">
                  <form action="" method="get">
                        <div class="relative w-full">
                              <button class="absolute left-0 top-1/2 -translate-y-1/2">
                                    <span
                                          class="absolute top-[50%] left-0 inline-flex -translate-y-1/2 items-center gap-0.5 rounded-lg px-[15px] py-[4.5px] text-xs -tracking-[0.2px] text-gray-500 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400">
                                          <i class="fa-solid fa-search"></i>
                                    </span>
                              </button>

                              <input type="text" x-model="search" placeholder="Rechercher..."
                                    class="appearance-none w-full xl:w-[430px] rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-violet-600 focus:border-violet-600 py-2.5 pr-14 pl-12 outline-none" />
                        </div>
                  </form>
            </div>

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
                                    <span class="text-[18px]"><i class="fa-solid fa-bell"></i></span>
                              </a>

                              <!-- Dropdown Start -->
                              <div x-show="dropdownOpen"
                                    class="absolute -right-27 mt-2.5 flex h-90 w-75 flex-col rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark sm:right-0 sm:w-80">
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
                                    <span class="text-[18px]"><i class="fa-solid fa-comment"></i></span>
                              </a>

                              <!-- Dropdown Start -->
                              <div x-show="dropdownOpen"
                                    class="absolute -right-16 mt-2.5 flex h-90 w-75 flex-col rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark sm:right-0 sm:w-80">
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

                  <!-- User Area -->
                  <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">

                        <a class="flex items-center gap-4" href="#"
                              @click.prevent="dropdownOpen = ! dropdownOpen">
                              <span class="hidden text-right lg:block">
                                    @if (Auth::user()->user_type === 1)
                                          <span class="block text-sm font-medium text-black dark:text-white">
                                                {{ Auth::user()->name }} {{ Auth::user()->last_name }}</span>
                                          <span class="block text-xs font-medium">Administrateur</span>
                                    @elseif(Auth::user()->user_type === 2)
                                          <span class="block text-sm font-medium text-black dark:text-white">
                                                {{ Auth::user()->name }} {{ Auth::user()->last_name }}</span>
                                          <span class="block text-xs font-medium">Professeur</span>
                                    @elseif(Auth::user()->user_type === 3)
                                          <span class="block text-sm font-medium text-black dark:text-white">
                                                {{ Auth::user()->name }} {{ Auth::user()->last_name }}</span>
                                          <span class="block text-xs font-medium">Elève</span>
                                    @elseif(Auth::user()->user_type === 4)
                                          <span class="block text-sm font-medium text-black dark:text-white">
                                                {{ Auth::user()->name }} {{ Auth::user()->last_name }}</span>
                                          <span class="block text-xs font-medium">Parent</span>
                                    @endif

                              </span>
                              @php
                                    $basePictureProfileUrl = 'upload/profile/';
                                    $getAdminPictureProfile = Auth::user()->profile_picture;
                                    $profilePicture = !empty($getAdminPictureProfile)
                                        ? $basePictureProfileUrl . $getAdminPictureProfile
                                        : 'upload/default.jpg';
                              @endphp
                              <span class="h-12 w-12 rounded-full">
                                    <img src="{{ asset($profilePicture) }}" alt="{{ Auth::user()->name }}"
                                          class="h-12 w-12 rounded-full object-cover object-center" />
                              </span>
                              <span :class="dropdownOpen && 'rotate-180'" class="hidden fill-current sm:block">
                                    <i class="fa-solid fa-chevron-down"></i>
                              </span>
                        </a>
                        <!-- Dropdown Start -->
                        <div x-show="dropdownOpen"
                              class="absolute right-0 mt-4 flex w-62.5 flex-col rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                              <ul
                                    class="flex flex-col gap-5 border-b border-stroke px-6 py-7.5 dark:border-strokedark">
                                    @if (Auth::user()->user_type === 1)
                                          <li>
                                                <a href="{{ url('admin/account') }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                                      <span><i class="fa-solid fa-user"></i></span>
                                                      Mon profile
                                                </a>
                                          </li>
                                          <li>
                                                <a href="{{ url('admin/change_password') }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                                      <span><i class="fa-solid fa-lock"></i></span>
                                                      Mon mot de passe
                                                </a>
                                          </li>
                                          <li>
                                                <a href="{{ url('admin/settings') }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                                      <span><i class="fa-solid fa-gear"></i></span>
                                                      Paramètres
                                                </a>
                                          </li>
                                    @elseif(Auth::user()->user_type === 2)
                                          <li>
                                                <a href="{{ url('teacher/account') }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                                      <span><i class="fa-solid fa-user"></i></span>
                                                      Mon profile
                                                </a>
                                          </li>
                                          <li>
                                                <a href="{{ url('teacher/change_password') }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                                      <span><i class="fa-solid fa-lock"></i></span>
                                                      Mon mot de passe
                                                </a>
                                          </li>
                                    @elseif(Auth::user()->user_type === 3)
                                          <li>
                                                <a href="{{ url('student/account') }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                                      <span><i class="fa-solid fa-user"></i></span>
                                                      Mon profile
                                                </a>
                                          </li>
                                          <li>
                                                <a href="{{ url('student/change_password') }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                                      <span><i class="fa-solid fa-lock"></i></span>
                                                      Mon mot de passe
                                                </a>
                                          </li>
                                    @elseif(Auth::user()->user_type === 4)
                                          <li>
                                                <a href="{{ url('parent/account') }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                                      <span><i class="fa-solid fa-user"></i></span>
                                                      Mon profile
                                                </a>
                                          </li>
                                          <li>
                                                <a href="{{ url('parent/change_password') }}"
                                                      class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                                      <span><i class="fa-solid fa-lock"></i></span>
                                                      Changez votre mot de passe
                                                </a>
                                          </li>
                                    @endif
                              </ul>
                              <a href="{{ url('logout') }}"
                                    class="flex items-center gap-3.5 px-6 py-4 text-sm font-medium duration-300 ease-in-out hover:text-primary lg:text-base">
                                    <span class="text-[22px]">
                                          <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    </span>
                                    Déconnexion
                              </a>
                        </div>
                        <!-- Dropdown End -->
                  </div>
                  <!-- User Area -->
            </div>
      </div>
</header>
