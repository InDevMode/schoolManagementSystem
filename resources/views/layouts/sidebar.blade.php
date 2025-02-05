<aside
    :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
    class="absolute left-0 top-0 z-9999 flex h-screen w-72.5 flex-col overflow-y-hidden bg-black duration-300 ease-linear dark:bg-boxdark lg:static lg:translate-x-0"
    @click.outside="sidebarToggle = false">
    <!-- SIDEBAR HEADER -->
    <div class="flex items-center justify-between gap-2 px-6 py-5.5 lg:py-6.5">
        <a href="{{ url('admin/dashboard') }}">
            <img src="{{ asset('public/images/logo.png') }}" alt="Logo"/>
        </a>
        <button
            class="block lg:hidden"
            @click.stop="sidebarToggle = !sidebarToggle"
        >
            <span class="text-[20px]"><i class="fa-solid fa-arrow-left"></i></span>
        </button>
    </div>
    <!-- SIDEBAR HEADER -->
    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <!-- Sidebar Menu -->
        <nav
            class="mt-5 px-4 py-4 lg:px-6"
            x-data="{selected: $persist('dashboard')}">
            <!-- Menu Group -->
            <div>
                <h3 class="mb-4 ml-4 text-sm font-medium text-bodydark2">
                    <span
                        class="self-center uppercase font-bold whitespace-nowrap">{{ Auth::user()->name }} {{ Auth::user()->last_name }}</span>
                </h3>
                <ul class="mb-6 flex flex-col gap-1.5">
                    <!-- Menu Item Dashboard -->
                    @if(Auth::user()->user_type === 1)
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('admin/dashboard') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'dashboard' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-house-chimney"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Dashboard</span>
                            <span
                                class="inline-flex items-center justify-center me-2 px-2.5 py-0.5 rounded text-xs font-medium {{ Request::Segment(2) == 'dashboard' ? 'bg-slate-900 text-bodydark1' : 'bg-slate-700'}}">{{ Request::Segment(1) }}</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'admin' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('admin/admin/list') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'admin' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-user-shield"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'admin' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Administrateurs</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'teacher' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('admin/teacher/list') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'teacher' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-user-tie"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'teacher' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Professeurs</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'student' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('admin/student/list') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'student' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-user-graduate"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'student' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Elèves</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'parent' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('admin/parent/list') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'parent' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-person-breastfeeding"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'parent' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Parents</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'class' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('admin/class/list') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'class' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-landmark"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'class' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Classes</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'subject' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('admin/subject/list') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'subject' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-book-open-reader"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'subject' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Matières</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                            href="#"
                            @click.prevent="selected = (selected === 'assign' ? '':'assign')"
                            :class="{ 'bg-graydark dark:bg-meta-4': (selected === 'assign') || (page === 'ecommerce' || page === 'analytics' || page === 'stocks') }"
                        >
                            <span class="text-[18px]"><i class="fa-solid fa-arrows-spin"></i></span>
                            <span>Assignations</span>
                            <span class="text-[18px] absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                  :class="{ 'rotate-180': (selected === 'assign') }"><i
                                    class="fa-solid fa-chevron-down"></i></span>
                        </a>
                        <!-- Dropdown Menu Start -->
                        <div
                            class="translate transform overflow-hidden"
                            :class="(selected === 'assign') ? 'block' :'hidden'"
                        >
                            <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                <li>
                                    <a
                                        class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'assign_class' ? 'bg-violet-600' : 'text-bodydark1' }}"
                                        href="{{ url('admin/assign_class/list') }}"
                                    >
                                        <span
                                            class="text-[18px] {{ Request::Segment(2) == 'assign_class' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                                class="fa-solid fa-chevron-right"></i></span>
                                        <span
                                            class="{{ Request::Segment(2) == 'assign_class' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Classes</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'assign_subject' ? 'bg-violet-600' : 'text-bodydark1' }}"
                                        href="{{ url('admin/assign_subject/list') }}"
                                    >
                                        <span
                                            class="text-[18px] {{ Request::Segment(2) == 'assign_subject' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                                class="fa-solid fa-chevron-right"></i></span>
                                        <span
                                            class="{{ Request::Segment(2) == 'assign_subject' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Matières</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'class_timetable' ? 'bg-violet-600' : 'text-bodydark1' }}"
                                        href="{{ url('admin/class_timetable/list') }}"
                                    >
                                        <span
                                            class="text-[18px] {{ Request::Segment(2) == 'class_timetable' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                                class="fa-solid fa-chevron-right"></i></span>
                                        <span
                                            class="{{ Request::Segment(2) == 'class_timetable' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Horaires</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                        <!-- Dropdown Menu End -->
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                            href="#"
                            @click.prevent="selected = (selected === 'examinations' ? '':'examinations')"
                            :class="{ 'bg-graydark dark:bg-meta-4': (selected === 'examinations') || (page === 'ecommerce' || page === 'analytics' || page === 'stocks') }"
                        >
                            <span class="text-[18px]"><i class="fa-solid fa-flask-vial"></i></span>
                            <span>Evaluations</span>
                            <span class="text-[18px] absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                  :class="{ 'rotate-180': (selected === 'examinations') }"><i
                                    class="fa-solid fa-chevron-down"></i></span>
                        </a>
                        <!-- Dropdown Menu Start -->
                        <div
                            class="translate transform overflow-hidden"
                            :class="(selected === 'examinations') ? 'block' :'hidden'"
                        >
                            <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                <li>
                                    <a
                                        class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'examinations' ? 'bg-violet-600' : 'text-bodydark1' }}"
                                        href="{{ url('admin/examinations/list') }}"
                                    >
                                        <span
                                            class="text-[18px] {{ Request::Segment(2) == 'examinations' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                                class="fa-solid fa-chevron-right"></i></span>
                                        <span
                                            class="{{ Request::Segment(2) == 'examinations' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Examens</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'assign_subject' ? 'bg-violet-600' : 'text-bodydark1' }}"
                                        href="{{ url('admin/assign_subject/list') }}"
                                    >
                                        <span
                                            class="text-[18px] {{ Request::Segment(2) == 'assign_subject' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                                class="fa-solid fa-chevron-right"></i></span>
                                        <span
                                            class="{{ Request::Segment(2) == 'assign_subject' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Matières</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'class_timetable' ? 'bg-violet-600' : 'text-bodydark1' }}"
                                        href="{{ url('admin/class_timetable/list') }}"
                                    >
                                        <span
                                            class="text-[18px] {{ Request::Segment(2) == 'class_timetable' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                                class="fa-solid fa-chevron-right"></i></span>
                                        <span
                                            class="{{ Request::Segment(2) == 'class_timetable' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Horaires</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                        <!-- Dropdown Menu End -->
                    </li>

                    @elseif(Auth::user()->user_type === 2)
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('teacher/dashboard') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'dashboard' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-house-chimney"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Dashboard</span>
                            <span
                                class="inline-flex items-center justify-center me-2 px-2.5 py-0.5 rounded text-xs font-medium {{ Request::Segment(2) == 'dashboard' ? 'bg-slate-900 text-bodydark1' : 'bg-slate-700'}}">{{ Request::Segment(1) }}</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'account' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('teacher/account') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'account' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-circle-user"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'account' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Mon Profile</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_student' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('teacher/my_student') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'my_student' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-user-graduate"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'my_student' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Mes Elèves</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'class_subject' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('teacher/class_subject') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'class_subject' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-landmark"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'class_subject' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Classes & Matières</span>
                        </a>
                    </li>
                    @elseif(Auth::user()->user_type === 3)
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('student/dashboard') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'dashboard' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-house-chimney"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Dashboard</span>
                            <span
                                class="inline-flex items-center justify-center me-2 px-2.5 py-0.5 rounded text-xs font-medium {{ Request::Segment(2) == 'dashboard' ? 'bg-slate-900 text-bodydark1' : 'bg-slate-700'}}">{{ Request::Segment(1) }}</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'account' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('student/account') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'account' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-circle-user"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'account' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Mon Profile</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_subject' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('student/my_subject') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'my_subject' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-landmark"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'my_subject' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Mes Cours</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_timetable' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('student/my_timetable') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'my_timetable' ? 'text-bodydark1' : 'text-bodydark1'}}"><i class="fa-solid fa-calendar-days"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'my_timetable' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Mes Programmes</span>
                        </a>
                    </li>
                    @elseif(Auth::user()->user_type === 4)
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('parent/dashboard') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'dashboard' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-house-chimney"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Dashboard</span>
                            <span
                                class="inline-flex items-center justify-center me-2 px-2.5 py-0.5 rounded text-xs font-medium {{ Request::Segment(2) == 'dashboard' ? 'bg-slate-900 text-bodydark1' : 'bg-slate-700'}}">{{ Request::Segment(1) }}</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'account' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('parent/account') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'account' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-circle-user"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'account' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Mon Profile</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_student' ? 'bg-violet-600' : 'text-bodydark1' }}"
                            href="{{ url('parent/my_student') }}"
                        >
                            <span
                                class="text-[18px] {{ Request::Segment(2) == 'my_student' ? 'text-bodydark1' : 'text-bodydark1'}}"><i
                                    class="fa-solid fa-circle-user"></i></span>
                            <span
                                class="{{ Request::Segment(2) == 'my_student' ? 'group-hover:text-bodydark1' : 'text-bodydark1'}}">Mes Elèves</span>
                        </a>
                    </li>
                    @endif
                    <ul>
                        <li class="absolute bottom-2 left-0 px-4 py-2 w-full">
                            <a href="{{ url('logout') }}"
                               class="flex items-center px-4 py-2 text-bodydark1 rounded-md border-2 border-violet-600 hover:text-bodydark1 hover:bg-violet-700 bg-violet-600 group transition-all duration-300 ease-out">
                                <span
                                    class="flex-shrink-0 text-bodydark1 transition group-hover:border-white duration-75 group-hover:text-bodydark1 text-[18px]">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                </span>
                                <span
                                    class="flex-1 ms-3 whitespace-nowrap group-hover:text-bodydark1 dark:group-hover:text-bodydark1">Déconnexion</span>
                            </a>
                        </li>
                    </ul>
                </ul>
            </div>
        </nav>
        <!-- Sidebar Menu -->
    </div>
</aside>
