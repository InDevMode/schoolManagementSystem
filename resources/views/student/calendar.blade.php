@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Mon calendrier
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-calendar-days"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('student/dashboard') }}"> Dashboard</a>
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
    document.addEventListener('DOMContentLoaded', function() {

        const events = new Array();

        @foreach($getMyCalendar as $myCalendar)
        @foreach($myCalendar['weeks'] as $weekCalendar)
            events.push({
                title: '{{ $myCalendar['name'] }}',
                daysOfWeek: [ {{ $weekCalendar['day'] }} ],
                startTime: '{{ $weekCalendar['start_time'] }}',
                endTime: '{{ $weekCalendar['end_time'] }}',
            });
        @endforeach
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
                today:    'Aujourd\'hui',
                month:    'Mois',
                week:     'Semaine',
                day:      'Jour',
                list:     'Liste'
            },
            allDayText: 'Journée',
            eventDisplay: 'block',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            },
            events: events,
            initialView: 'timeGridWeek'
        });
        calendar.render();
    });
</script>

@endsection
