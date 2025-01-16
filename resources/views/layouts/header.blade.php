<nav class="fixed top-0 w-full z-50 bg-white shadow-lg border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <span class="w-6 h-6 text-gray-800 dark:text-white">
                            <i class="fa-solid fa-bars-staggered"></i>
                        </span>
                    <span class="sr-only">Open sidebar</span>
                </button>
                <a href="{{ url('admin/dashboard') }}" class="flex ms-2 md:me-24 font-bold text-lg">
                    <img src="public/images/logo.png" class="w-44" alt="SCHOOLMANAGEMENT"/>
                </a>
            </div>
            <div class="flex space-x-4 items-center text-violet-500">
                @if(Auth::user()->user_type === 1)
                <div>
                    <button type="button" class="p-2"
                        <span class=""><i class="fa-solid fa-2x fa-bell"></i></span>
                    </button>
                </div>
                @endif
                <div class="flex items-center justify-center rounded-full">
                    <div>
                        <button type="button"
                                class="p-2"
                                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <span class="">
                                    <i class="fa-solid fa-2x fa-user-graduate"></i>
                            </span>
                        </button>
                    </div>
                    <div
                        class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                        id="dropdown-user">
                        <div class="px-4 py-3" role="none">
                            <p class="text-sm text-gray-900 dark:text-white" role="none">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                        <ul class="py-1" role="none">
                            @if(Auth::user()->user_type === 1)
                            <li>
                                <a href="{{ url('admin/dashboard') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:text-white hover:bg-violet-500 transition duration-300 ease-out"
                                   role="menuitem">Dashboard</a>
                            </li>
                            <li>
                                <a href="#"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:text-white hover:bg-violet-500 transition duration-300 ease-out"
                                   role="menuitem">Paramètres</a>
                            </li>
                            @elseif(Auth::user()->user_type === 2)
                            <li>
                                <a href="{{ url('teacher/dashboard') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:text-white hover:bg-violet-500 transition duration-300 ease-oute"
                                   role="menuitem">Dashboard</a>
                            </li>
                            @elseif(Auth::user()->user_type === 3)
                            <li>
                                <a href="{{ url('student/dashboard') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:text-white hover:bg-violet-500 transition duration-300 ease-out"
                                   role="menuitem">Dashboard</a>
                            </li>
                            <li>
                                <a href="#"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:text-white hover:bg-violet-500 transition duration-300 ease-out"
                                   role="menuitem">Cours</a>
                            </li>
                            @elseif(Auth::user()->user_type === 4)
                            <li>
                                <a href="{{ url('parent/dashboard') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:text-white hover:bg-violet-500 transition duration-300 ease-out"
                                   role="menuitem">Dashboard</a>
                            </li>
                            @endif
                            <li>
                                <a href="{{ url('logout') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:text-white hover:bg-violet-500 transition duration-300 ease-out"
                                   role="menuitem">Déconnexion</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
