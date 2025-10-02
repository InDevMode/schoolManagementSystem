@extends('layouts.app')

@section('content')
      <div class="m-5">
            <div class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between">
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

            <div class="card p-5">
                  <div id="calendar"></div>
            </div>
      </div>
@endsection

@section('script')
      <script src="{{ url('public/fullcalendar/index.global.min.js') }}"></script>
      <script src="{{ url('public/fullcalendar/locales/fr.global.min.js') }}"></script>

      <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                  const events = new Array();

                  // Événements des matières (cours)
                  @foreach ($getMyTimetable as $value)
                        @foreach ($value['weeks'] as $week)
                              @if (!empty($week['start_time']) && !empty($week['end_time']))
                                    events.push({
                                          title: '{{ $value['name'] }}',
                                          startTime: '{{ date('H:i:s', strtotime($week['start_time'])) }}',
                                          endTime: '{{ date('H:i:s', strtotime($week['end_time'])) }}',
                                          daysOfWeek: [{{ $week['day'] }}],
                                          color: '#7c3aed',
                                          url: '{{ url('student/my_exam_timetable') }}',
                                          extendedProps: {
                                                displayTime: '{{ date('H\hi', strtotime($week['start_time'])) }} - {{ date('H\hi', strtotime($week['end_time'])) }}'
                                          }
                                    });
                              @endif
                        @endforeach
                  @endforeach

                  // Événements d'examens
                  @foreach ($getExamTimetable as $valueExam)
                        @foreach ($valueExam['exams'] as $exam)
                              <?php
                              $startTime = date('H:i:s', strtotime($exam['start_time']));
                              $endTime = date('H:i:s', strtotime($exam['end_time']));
                              ?>
                              events.push({
                                    title: '{{ $valueExam['name'] }} ({{ $exam['subject_name'] }})',
                                    start: '{{ $exam['exam_date'] }}T{{ $startTime }}',
                                    end: '{{ $exam['exam_date'] }}T{{ $endTime }}',
                                    color: '#34d399',
                                    // Ajout d'une propriété custom pour l'affichage du lieu si disponible
                                    extendedProps: {
                                          room_number: '{{ !empty($exam['room_number']) ? $exam['room_number'] : '' }}'
                                    }
                              });
                        @endforeach
                  @endforeach

                  const calendarID = document.getElementById("calendar");
                  const calendar = new FullCalendar.Calendar(calendarID, {
                        locale: 'fr',
                        initialDate: '<?= date('Y-m-d') ?>',
                        navLinks: true,
                        editable: false,
                        headerToolbar: {
                              left: 'prev,next today',
                              center: 'title',
                              right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                        },
                        buttonText: {
                              today: 'Aujourd\'hui',
                              month: 'Mois',
                              week: 'Semaine',
                              day: 'Jour',
                              list: 'Liste'
                        },
                        allDayText: 'Jour',
                        eventDisplay: 'auto',
                        eventTimeFormat: {
                              hour: '2-digit',
                              minute: '2-digit',
                              hour12: false
                        },
                        events: events,
                        initialView: 'timeGridWeek',
                        eventContent: function(arg) {
                              let html = `<div class="p-1"><b>${arg.event.title}</b>`;

                              // Ajoute l'heure pour les cours
                              if (arg.event.extendedProps.displayTime) {
                                    html +=
                                          `<br><span><i class="fa-solid fa-clock mr-1"></i>${arg.event.extendedProps.displayTime}</span>`;
                              }

                              // Ajoute l'heure et la salle pour les examens
                              if (arg.event.start && arg.event.extendedProps.room_number) {
                                    const start = new Date(arg.event.start);
                                    const end = new Date(arg.event.end);
                                    const formattedStart =
                                          `${String(start.getHours()).padStart(2, '0')}h${String(start.getMinutes()).padStart(2, '0')}`;
                                    const formattedEnd =
                                          `${String(end.getHours()).padStart(2, '0')}h${String(end.getMinutes()).padStart(2, '0')}`;
                                    html +=
                                          `<br><span><i class="fa-solid fa-clock mr-1"></i>${formattedStart} - ${formattedEnd}</span>`;
                                    html +=
                                          `<br><span><i class="fa-solid fa-location-dot mr-1"></i> Salle : ${arg.event.extendedProps.room_number}</span>`;
                              }
                              html += `</div>`;

                              return {
                                    html: html
                              };
                        }
                  });
                  calendar.render();
            });
      </script>
@endsection
