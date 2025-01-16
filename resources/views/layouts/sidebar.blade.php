<aside id="logo-sidebar"
       class="fixed top-0 left-0 z-40 shadow-lg w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0"
       aria-label="Sidebar">
    <div
        class="h-full px-3 pb-4 overflow-y-auto scrollbar-thin scrollbar-thumb-blue-500 scrollbar-track-gray-200 bg-white text-white">
        <ul class="space-y-2 font-semibold">
            @if(Auth::user()->user_type == 1)
            <li>
                <a href="{{ url('admin/dashboard') }}"
                   class="flex items-center p-2 rounded hover:bg-violet-600 transition-all duration-700 ease-out group {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-500' : 'text-violet-500' }}">
                    <span
                        class="flex-shrink-0 transition duration-75 group-hover:text-white {{ Request::Segment(2) == 'dashboard' ? 'text-white' : 'text-violet-500'}}">
                        <i class="fa-solid fa-house-chimney"></i>
                    </span>
                    <span
                        class="flex-1 ms-3 whitespace-nowrap group-hover:text-white {{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-white' : 'text-violet-500'}}">Dashboard</span>
                    <span
                        class="inline-flex items-center justify-center me-2 px-2.5 py-0.5 rounded bg-violet-100 text-violet-800 text-xs font-medium"> {{ Request::Segment(1) }}</span>
                </a>
            </li>
            <li>
                <a href="{{ url('admin/admin/list') }}"
                   class="flex items-center p-2 rounded hover:bg-violet-600 transition-all duration-700 ease-out group {{ Request::Segment(2) == 'admin' ? 'bg-violet-500' : 'text-violet-500'}}">
                    <span
                        class="flex-shrink-0 transition duration-75 group-hover:text-white {{ Request::Segment(2) == 'admin' ? 'text-white' : ' text-violet-500'}}">
                       <i class="fa-solid fa-user-secret"></i>
                    </span>
                    <span
                        class="flex-1 ms-3 whitespace-nowrap group-hover:text-white {{ Request::Segment(2) == 'admin' ? 'group-hover:text-white' : 'text-violet-500'}}">Administrateurs</span>
                </a>
            </li>
            <li>
                <a href="{{ url('admin/class/list') }}"
                   class="flex items-center p-2 rounded hover:bg-violet-600 transition-all duration-700 ease-out group {{ Request::Segment(2) == 'class' ? 'bg-violet-500' : 'text-violet-500'}}">
                    <span class="flex-shrink-00 transition duration-75 group-hover:text-white {{ Request::Segment(2) == 'class' ? 'text-white' : ' text-violet-500'}}">
                        <i class="fa-solid fa-landmark"></i>
                    </span>
                    <span class="flex-1 ms-3 whitespace-nowrap group-hover:text-white {{ Request::Segment(2) == 'class' ? 'group-hover:text-white' : 'text-violet-500'}}">Classes</span>
                </a>
            </li>
            <li>
                <a href="{{ url('admin/subject/list') }}"
                   class="flex items-center p-2 rounded hover:bg-violet-600 transition-all duration-700 ease-out group {{ Request::Segment(2) == 'subject' ? 'bg-violet-500' : 'text-violet-500'}}">
                    <span class="flex-shrink-0 transition duration-75 group-hover:text-white {{ Request::Segment(2) == 'subject' ? 'text-white' : ' text-violet-500'}}">
                        <i class="fa-solid fa-book-open-reader"></i>
                    </span>
                    <span class="flex-1 ms-3 whitespace-nowrap group-hover:text-white {{ Request::Segment(2) == 'subject' ? 'group-hover:text-white' : 'text-violet-500'}}">Matières</span>
                </a>
            </li>
            <li>
                <a href="{{ url('admin/assign_subject/list') }}"
                   class="flex items-center p-2 rounded hover:bg-violet-600 transition-all duration-700 ease-out group {{ Request::Segment(2) == 'assign_subject' ? 'bg-violet-500' : 'text-violet-500'}}">
                    <span class="flex-shrink-0 transition duration-75 group-hover:text-white {{ Request::Segment(2) == 'assign_subject' ? 'text-white' : ' text-violet-500'}}">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </span>
                    <span class="flex-1 ms-3 whitespace-nowrap group-hover:text-white {{ Request::Segment(2) == 'assign_subject' ? 'group-hover:text-white' : 'text-violet-500'}}">Assignations</span>
                </a>
            </li>
            @elseif(Auth::user()->user_type == 2)
            <li>
                <a href="{{ url('teacher/dashboard') }}"
                   class="flex items-center p-2 rounded hover:bg-violet-600 transition-all duration-700 ease-out group {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-500' : 'text-violet-500' }}">
                    <span
                        class="flex-shrink-0 transition duration-75 group-hover:text-white {{ Request::Segment(2) == 'dashboard' ? 'text-white' : 'text-violet-500'}}">
                        <i class="fa-solid fa-house-chimney"></i>
                    </span>
                    <span
                        class="flex-1 ms-3 whitespace-nowrap group-hover:text-white {{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-white' : 'text-violet-500'}}">Dashboard</span>
                    <span
                        class="inline-flex items-center justify-center me-2 px-2.5 py-0.5 rounded bg-violet-100 text-violet-800 text-xs font-medium"> {{ Request::Segment(1) }}</span>
                </a>
            </li>
            @elseif(Auth::user()->user_type == 3)
            <li>
                <a href="{{ url('student/dashboard') }}"
                   class="flex items-center p-2 rounded hover:bg-violet-600 transition-all duration-700 ease-out group {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-500' : 'text-violet-500' }}">
                    <span
                        class="flex-shrink-0 transition duration-75 group-hover:text-white {{ Request::Segment(2) == 'dashboard' ? 'text-white' : 'text-violet-500'}}">
                        <i class="fa-solid fa-house-chimney"></i>
                    </span>
                    <span
                        class="flex-1 ms-3 whitespace-nowrap group-hover:text-white {{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-white' : 'text-violet-500'}}">Dashboard</span>
                    <span
                        class="inline-flex items-center justify-center me-2 px-2.5 py-0.5 rounded bg-violet-100 text-violet-800 text-xs font-medium"> {{ Request::Segment(1) }}</span>
                </a>
            </li>
            @elseif(Auth::user()->user_type == 4)
            <li>
                <a href="{{ url('parent/dashboard') }}"
                   class="flex items-center p-2 rounded hover:bg-violet-600 transition-all duration-700 ease-out group {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-500' : 'text-violet-500' }}">
                    <span
                        class="flex-shrink-0 transition duration-75 group-hover:text-white {{ Request::Segment(2) == 'dashboard' ? 'text-white' : 'text-violet-500'}}">
                        <i class="fa-solid fa-house-chimney"></i>
                    </span>
                    <span
                        class="flex-1 ms-3 whitespace-nowrap group-hover:text-white {{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-white' : 'text-violet-500'}}">Dashboard</span>
                    <span
                        class="inline-flex items-center justify-center me-2 px-2.5 py-0.5 rounded bg-violet-100 text-violet-800 text-xs font-medium"> {{ Request::Segment(1) }}</span>
                </a>
            </li>
            @endif
            <ul>
                <li class="absolute bottom-2 left-0 px-3 w-full">
                    <a href="{{ url('logout') }}"
                       class="flex items-center p-2 text-violet-500 rounded border-2 border-violet-500 hover:text-white hover:bg-violet-600 bg-white group transition-all duration-300 ease-out">
                        <span
                            class="flex-shrink-0 text-violet-500 transition group-hover:border-white duration-75 group-hover:text-white">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </span>

                        <span
                            class="flex-1 ms-3 whitespace-nowrap group-hover:text-white dark:group-hover:text-white">Déconnexion</span>
                    </a>
                </li>
            </ul>
        </ul>
    </div>
</aside>
