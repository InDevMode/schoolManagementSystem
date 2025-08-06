<aside :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
    class="absolute left-0 top-0 z-9999 flex h-screen w-72.5 flex-col overflow-y-hidden bg-white duration-300 ease-linear dark:bg-boxdark lg:static lg:translate-x-0"
    @click.outside="sidebarToggle = false">
    <!-- SIDEBAR HEADER -->
    @php
        $links = [
            1 => url('admin/dashboard'), // Admin
            2 => url('teacher/dashboard'), // Teacher
            3 => url('student/dashboard'), // Student
            4 => url('parent/dashboard'), // Parent
        ];
        $link = $links[Auth::user()->user_type] ?? url('login'); // Lien de secours
    @endphp

    <div class="flex items-center justify-between gap-2 px-6 py-5.5">
        <a href="{{ $link }}">
            <img src="{{ asset('public/images/managment.png') }}" alt="Logo" />
        </a>

        <button class="block lg:hidden" @click.stop="sidebarToggle = !sidebarToggle">
            <span class="text-[20px]"><i class="fa-solid fa-arrow-left"></i></span>
        </button>
    </div>

    <!-- SIDEBAR HEADER -->
    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <!-- Sidebar Menu -->
        <nav class="px-4 py-4 lg:px-6" x-data="{selected: $persist('dashboard')}">
            <!-- Menu Group -->
            <div>
                <h3 class="mb-4 ml-4 text-sm font-medium text-bodydark2">
                    <span class="self-center font-bold whitespace-nowrap">{{ Auth::user()->name }}
                        {{ Auth::user()->last_name }}</span>
                </h3>
                <ul class="mb-6 flex flex-col gap-1.5">
                    <!-- Menu Item Dashboard -->
                    @if(Auth::user()->user_type === 1)
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('admin/dashboard') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded  {{ Request::Segment(2) == 'dashboard' ? ' bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-house-chimney  text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Dashboard</span>
                                <span
                                    class="inline-flex items-center justify-center me-2 px-1 py-0.5 rounded text-xs font-medium {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100'}}">{{ Request::Segment(1) }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'admin' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('admin/admin/list') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'admin' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-user-shield text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'admin' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Administrateurs</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'teacher' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('admin/teacher/list') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'teacher' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-user-tie text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'teacher' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Professeurs</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'student' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('admin/student/list') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'student' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-user-graduate text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'student' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Apprenants</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'parent' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('admin/parent/list') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'parent' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-person-breastfeeding text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'parent' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Parents</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'class' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('admin/class/list') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'class' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-landmark text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'class' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Classes</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'subject' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('admin/subject/list') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'subject' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-book-open-reader text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'subject' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Matières</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-violet-600 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                                href="#" @click.prevent="selected = (selected === 'assign' ? '':'assign')"
                                :class="{ 'bg-gray-100 dark:bg-meta-4': (selected === 'assign')}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded bg-violet-100 text-violet-600"><i
                                        class="fa-solid fa-arrows-spin text-[18px]"></i></span>
                                <span class="group-hover:text-bodydark1 dark:text-bodydark1">Assignations</span>
                                <span
                                    class="text-[18px] py-1 px-2 rounded bg-violet-100 text-violet-600 transition duration-700 absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                    :class="{ 'rotate-180 transition duration-700': (selected === 'assign') }"><i
                                        class="fa-solid fa-chevron-down"></i></span>
                            </a>
                            <!-- Dropdown Menu Start -->
                            <div class="translate transform overflow-hidden transition duration-700"
                                :class="(selected === 'assign') ? 'block' :'hidden'">
                                <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'assign_class' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/assign_class/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(2) == 'assign_class' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(2) == 'assign_class' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Classes</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'assign_subject' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/assign_subject/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(2) == 'assign_subject' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(2) == 'assign_subject' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Matières</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'class_timetable' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/class_timetable/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(2) == 'class_timetable' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(2) == 'class_timetable' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Horaires</span>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                            <!-- Dropdown Menu End -->
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-violet-600 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                                href="#" @click.prevent="selected = (selected === 'examinations' ? '':'examinations')"
                                :class="{ 'bg-gray-100 dark:bg-meta-4': (selected === 'examinations')}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded bg-violet-100 text-violet-600"><i
                                        class="fa-solid fa-flask-vial text-[18px]"></i></span>
                                <span class="group-hover:text-bodydark1 dark:text-bodydark1">Evaluations</span>
                                <span
                                    class="text-[18px] py-1 px-2 rounded bg-violet-100 text-violet-600 transition duration-700 absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                    :class="{ 'rotate-180 transition duration-700': (selected === 'examinations') }"><i
                                        class="fa-solid fa-chevron-down"></i></span>
                            </a>
                            <!-- Dropdown Menu Start -->
                            <div class="translate transform overflow-hidden transition duration-700"
                                :class="(selected === 'examinations') ? 'block' :'hidden'">
                                <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'exam' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/examinations/exam/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'exam' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'exam' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Examens</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'schedule' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/examinations/schedule/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'schedule' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'schedule' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Programmations</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'marks_register' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/examinations/marks_register/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'marks_register' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'marks_register' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Registres</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'marks_grade' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/examinations/marks_grade/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'marks_grade' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'marks_grade' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Notes</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Dropdown Menu End -->
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-violet-600 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                                href="#" @click.prevent="selected = (selected === 'attendance' ? '':'attendance')"
                                :class="{ 'bg-gray-100 dark:bg-meta-4': (selected === 'attendance')}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded bg-violet-100 text-violet-600"><i
                                        class="fa-solid fa-user-check text-[18px]"></i></span>
                                <span class="group-hover:text-bodydark1 dark:text-bodydark1">Présence</span>
                                <span
                                    class="text-[18px] py-1 px-2 rounded bg-violet-100 text-violet-600 transition duration-700 absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                    :class="{ 'rotate-180 transition duration-700': (selected === 'attendance') }"><i
                                        class="fa-solid fa-chevron-down"></i></span>
                            </a>
                            <!-- Dropdown Menu Start -->
                            <div class="translate transform overflow-hidden transition duration-700"
                                :class="(selected === 'attendance') ? 'block' :'hidden'">
                                <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'student' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/attendance/student/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'student' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'student' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Apprenants</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'report' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/attendance/report') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'report' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'report' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Rapports</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Dropdown Menu End -->
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-violet-600 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                                href="#" @click.prevent="selected = (selected === 'communicate' ? '':'communicate')"
                                :class="{ 'bg-gray-100 dark:bg-meta-4': (selected === 'communicate')}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded bg-violet-100 text-violet-600"><iconify-icon
                                        icon="mdi:bell" width="24" height="24"></iconify-icon>
                                </span>
                                <span class="group-hover:text-bodydark1 dark:text-bodydark1">Notifications</span>
                                <span
                                    class="text-[18px] py-1 px-2 rounded bg-violet-100 text-violet-600 transition duration-700 absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                    :class="{ 'rotate-180 transition duration-700': (selected === 'communicate') }"><i
                                        class="fa-solid fa-chevron-down"></i></span>
                            </a>
                            <!-- Dropdown Menu Start -->
                            <div class="translate transform overflow-hidden transition duration-700"
                                :class="(selected === 'communicate') ? 'block' :'hidden'">
                                <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'noticeboard' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/communicate/noticeboard/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'noticeboard' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'noticeboard' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Affichages</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'send_mail' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/communicate/send_mail') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'send_mail' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'send_mail' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mails</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Dropdown Menu End -->
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-violet-600 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                                href="#" @click.prevent="selected = (selected === 'practicalworks' ? '':'practicalworks')"
                                :class="{ 'bg-gray-100 dark:bg-meta-4': (selected === 'practicalworks')}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded bg-violet-100 text-violet-600"><iconify-icon
                                        icon="mdi:home-edit" width="24" height="24"></iconify-icon>
                                </span>
                                <span class="group-hover:text-bodydark1 dark:text-bodydark1">Devoirs </span>
                                <span
                                    class="text-[18px] py-1 px-2 rounded bg-violet-100 text-violet-600 transition duration-700 absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                    :class="{ 'rotate-180 transition duration-700': (selected === 'practicalworks') }"><i
                                        class="fa-solid fa-chevron-down"></i></span>
                            </a>
                            <!-- Dropdown Menu Start -->
                            <div class="translate transform overflow-hidden transition duration-700"
                                :class="(selected === 'practicalworks') ? 'block' :'hidden'">
                                <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'homework' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/practicalworks/homework/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'homework' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'homework' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Travaux Maison</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(4) == 'reports' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/practicalworks/homework/reports') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(4) == 'reports' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(4) == 'reports' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Rapports</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Dropdown Menu End -->
                        </li>
                         <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-violet-600 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                                href="#" @click.prevent="selected = (selected === 'feescollections' ? '':'feescollections')"
                                :class="{ 'bg-gray-100 dark:bg-meta-4': (selected === 'feescollections')}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded bg-violet-100 text-violet-600"><iconify-icon
                                        icon="mdi:cash-register" width="24" height="24"></iconify-icon>
                                </span>
                                <span class="group-hover:text-bodydark1 dark:text-bodydark1">Contributions </span>
                                <span
                                    class="text-[18px] py-1 px-2 rounded bg-violet-100 text-violet-600 transition duration-700 absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                    :class="{ 'rotate-180 transition duration-700': (selected === 'feescollections') }"><i
                                        class="fa-solid fa-chevron-down"></i></span>
                            </a>
                            <!-- Dropdown Menu Start -->
                            <div class="translate transform overflow-hidden transition duration-700"
                                :class="(selected === 'feescollections') ? 'block' :'hidden'">
                                <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'collections' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/feescollections/collections/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'collections' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'collections' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Percevoir</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'feescollects' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('admin/feescollections/feescollects/feesList') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'feescollects' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'feescollects' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Reçues</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Dropdown Menu End -->
                        </li>
                    @elseif(Auth::user()->user_type === 2)
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('teacher/dashboard') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-house-chimney text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Dashboard</span>
                                <span
                                    class="inline-flex items-center justify-center me-2 px-1 py-0.5 rounded text-xs font-medium {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100'}}">{{ Request::Segment(1) }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'account' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('teacher/account') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'account' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-circle-user text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'account' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mon
                                    Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_student' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('teacher/my_student') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_student' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-user-graduate text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_student' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Apprenants</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'class_subject' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('teacher/class_subject') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'class_subject' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-landmark text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'class_subject' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Classes
                                    & Matières</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_exam_timetable' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('teacher/my_exam_timetable') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_exam_timetable' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-flask-vial text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_exam_timetable' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Examens</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_calendar' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('teacher/my_calendar') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_calendar' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-calendar-days text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_calendar' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mon
                                    Calendrier</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'marks_register' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('teacher/marks_register') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'marks_register' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-registered text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'marks_register' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Registres</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_noticeboard' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('teacher/my_noticeboard') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_noticeboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}">
                                    <iconify-icon icon="mdi:bell-outline" width="24" height="24"></iconify-icon>
                                </span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_noticeboard' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Notifications</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-violet-600 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                                href="#" @click.prevent="selected = (selected === 'attendance' ? '':'attendance')"
                                :class="{ 'bg-gray-100 dark:bg-meta-4': (selected === 'attendance')}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded bg-violet-100 text-violet-600"><i
                                        class="fa-solid fa-user-check text-[18px]"></i></span>
                                <span class="group-hover:text-bodydark1 dark:text-bodydark1">Présence</span>
                                <span
                                    class="text-[18px] py-1 px-2 rounded bg-violet-100 text-violet-600 transition duration-700 absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                    :class="{ 'rotate-180 transition duration-700': (selected === 'attendance') }"><i
                                        class="fa-solid fa-chevron-down"></i></span>
                            </a>
                            <!-- Dropdown Menu Start -->
                            <div class="translate transform overflow-hidden transition duration-700"
                                :class="(selected === 'attendance') ? 'block' :'hidden'">
                                <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'student' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('teacher/attendance/student/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'student' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'student' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Apprenants</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'report' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('teacher/attendance/report') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'report' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'report' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Rapports</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Dropdown Menu End -->
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium text-violet-600 duration-300 ease-in-out hover:bg-violet-600 dark:hover:bg-meta-4"
                                href="#" @click.prevent="selected = (selected === 'practicalworks' ? '':'practicalworks')"
                                :class="{ 'bg-gray-100 dark:bg-meta-4': (selected === 'practicalworks')}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded bg-violet-100 text-violet-600"><iconify-icon
                                        icon="mdi:home-edit" width="24" height="24"></iconify-icon>
                                </span>
                                <span class="group-hover:text-bodydark1 dark:text-bodydark1">Travaux </span>
                                <span
                                    class="text-[18px] py-1 px-2 rounded bg-violet-100 text-violet-600 transition duration-700 absolute right-4 top-1/2 -translate-y-1/2 fill-current"
                                    :class="{ 'rotate-180 transition duration-700': (selected === 'practicalworks') }"><i
                                        class="fa-solid fa-chevron-down"></i></span>
                            </a>
                            <!-- Dropdown Menu Start -->
                            <div class="translate transform overflow-hidden transition duration-700"
                                :class="(selected === 'practicalworks') ? 'block' :'hidden'">
                                <ul class="mb-5.5 mt-4 flex flex-col gap-2.5 pl-8">
                                    <li>
                                        <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(3) == 'homework' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                            href="{{ url('teacher/practicalworks/homework/list') }}">
                                            <span
                                                class="text-[18px] py-1 px-2 rounded {{ Request::Segment(3) == 'homework' ? 'group-hover:text-bodydark1 text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}"><i
                                                    class="fa-solid fa-chevron-right"></i></span>
                                            <span
                                                class="{{ Request::Segment(3) == 'homework' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">De
                                                Maison</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Dropdown Menu End -->
                        </li>

                    @elseif(Auth::user()->user_type === 3)
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/dashboard') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-house-chimney text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Dashboard</span>
                                <span
                                    class="inline-flex items-center justify-center px-1 py-0.5 rounded text-xs font-medium {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100'}}">{{ Request::Segment(1) }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_calendar' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/my_calendar') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_calendar' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-calendar-days text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_calendar' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mon
                                    Calendrier</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'account' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/account') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'account' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-circle-user text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'account' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mon
                                    Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_subject' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/my_subject') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_subject' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-landmark text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_subject' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Cours</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_timetable' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/my_timetable') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_timetable' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-clock text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_timetable' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Programmes</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_exam_timetable' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/my_exam_timetable') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_exam_timetable' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><iconify-icon
                                        icon="mdi:test-tube" width="24" height="24"></iconify-icon></span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_exam_timetable' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Examens</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_exam_result' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/my_exam_result') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_exam_result' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}">
                                    <i class="fa-solid fa-square-poll-horizontal" width="24" height="24"></i>
                                </span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_exam_result' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Résultats</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_attendance' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/my_attendance') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_attendance' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}">
                                    <iconify-icon icon="mdi:calendar-check" width="24" height="24"></iconify-icon>
                                </span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_attendance' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Ma
                                    Présence</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_noticeboard' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/my_noticeboard') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_noticeboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}">
                                    <iconify-icon icon="mdi:bell" width="24" height="24"></iconify-icon>
                                </span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_noticeboard' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Notifications</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_homework' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/my_homework') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_homework' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}">
                                    <iconify-icon icon="mdi:home-edit" width="24" height="24"></iconify-icon>
                                </span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_homework' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Travaux</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_fees' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('student/my_fees') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_fees' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}">
                                    <i class="fa-solid fa-cash-register" width="24" height="24"></i>
                                </span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_fees' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Contributions</span>
                            </a>
                        </li>
                    @elseif(Auth::user()->user_type === 4)
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('parent/dashboard') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-house-chimney text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'dashboard' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Dashboard</span>
                                <span
                                    class="inline-flex items-center justify-center me-2 px-1 py-0.5 rounded text-xs font-medium {{ Request::Segment(2) == 'dashboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100'}}">{{ Request::Segment(1) }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'account' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('parent/account') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'account' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-circle-user text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'account' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mon
                                    Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_student' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('parent/my_student') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_student' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}"><i
                                        class="fa-solid fa-user-graduate text-[18px]"></i></span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_student' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Apprenants</span>
                            </a>
                        </li>
                        <li>
                            <a class="group relative flex items-center gap-2.5 rounded-md px-4 py-2 font-medium duration-300 ease-in-out hover:bg-violet-700 text-bodydark1 dark:hover:bg-meta-4 {{ Request::Segment(2) == 'my_noticeboard' ? 'bg-violet-600 text-bodydark1' : 'text-violet-600' }}"
                                href="{{ url('parent/my_noticeboard') }}">
                                <span
                                    class="w-10 h-10 flex items-center justify-center rounded {{ Request::Segment(2) == 'my_noticeboard' ? 'bg-violet-100 text-violet-600' : 'bg-violet-100 text-violet-600'}}">
                                    <iconify-icon icon="mdi:bell" width="24" height="24"></iconify-icon>
                                </span>
                                <span
                                    class="{{ Request::Segment(2) == 'my_noticeboard' ? 'group-hover:text-bodydark1' : 'group-hover:text-bodydark1 text-violet-600 dark:text-bodydark1'}}">Mes
                                    Notifications</span>
                            </a>
                        </li>
                    @endif
                    <ul>
                        <li class="absolute bottom-2 left-0 px-4 py-2 w-full">
                            <a href="{{ url('logout') }}"
                                class="flex items-center px-4 py-2 text-white rounded-md border-2 border-violet-600 dark:border-gray-800 dark:hover:bg-gray-900 hover:text-white hover:bg-violet-700 bg-violet-600 dark:bg-gray-800 group transition-all duration-300 ease-out">
                                <span
                                    class="flex items-center justify-center transition group-hover:border-white duration-75 group-hover:text-violet-600 w-10 h-10 py-1 px-2 rounded bg-violet-100 text-violet-600">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-[18px]"></i>
                                </span>
                                <span
                                    class="flex-1 ms-3 whitespace-nowrap group-hover:text-white dark:group-hover:text-white">Déconnexion</span>
                            </a>
                        </li>
                    </ul>
                </ul>
            </div>
        </nav>
        <!-- Sidebar Menu -->
    </div>
</aside>
