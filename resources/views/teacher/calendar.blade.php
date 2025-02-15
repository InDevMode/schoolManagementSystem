@extends('layouts.app')
@section('content')
@section('style')
<style type="text/css">
    .fc-daygrid-event {
        white-space: normal;
    }
</style>
@endsection

<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Son calendrier
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-calendar-days"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('teacher/dashboard') }}"> Dashboard</a>
                </li>
            </ol>
        </nav>
    </div>

    <div class="" id="calendar"></div>

</div>
@endsection

@section('script')
<script src="{{ url('public/fullcalendar/index.global.min.js') }}"></script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {

        const events = new Array();

        @foreach($getClassTeacherTimetable as $valueClassTeacher)
            <?php
            /** @var TYPE_NAME $valueClassTeacher */
            $startTime = date('G\h i\m\i\n', strtotime($valueClassTeacher->start_time));
            $endTime = date('G\h i\m\i\n', strtotime($valueClassTeacher->end_time));
            ?>
            events.push({
                title: '{{ $valueClassTeacher->class_name }} => {{ $valueClassTeacher->subject_name }}',
                daysOfWeek: [ {{ $valueClassTeacher->week_day }} ],
                startTime: '{{$valueClassTeacher->start_time }}',
                endTime: '{{ $valueClassTeacher->end_time }}',
                color: '#7c3aed',
            });
        @endforeach

        @foreach($getExamTimetableTeacher as $valueExam)
            <?php
            /** @var TYPE_NAME $valueExam */
            $startTime = date('G\h i\m\i\n', strtotime($valueExam['start_time']));
            $endTime = date('G\h i\m\i\n', strtotime($valueExam['end_time']));
            ?>
            events.push({
                title: '{{ $valueExam->class_name }} => {{ $valueExam->exam_name }} => {{ $valueExam->subject_name }} de {{ $startTime }} à  {{ $endTime }}',
                start: '{{ $valueExam->exam_date }}',
                end: '{{ $valueExam->exam_date }}',
                color: '#34d399',
                url : '{{ url('teacher/my_exam_timetable') }}',
            });
        @endforeach


        const calendarID = document.getElementById("calendar");
        const calendar = new FullCalendar.Calendar(calendarID, {
            locale: 'fr',
            initialDate: '<?=date('Y-m-d')?>',
            navLinks: true,
            editable: false,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour',
                list: 'Liste'
            },
            allDayText: 'Jour',
            eventDisplay: 'block',

            events: events,
            initialView: 'timeGridWeek'
        });
        calendar.render();
    });
</script>

@endsection
