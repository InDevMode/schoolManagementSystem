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

            <div class="" id="calendar"></div>

      </div>
@endsection

@section('script')
      <script src="{{ url('public/fullcalendar/index.global.min.js') }}"></script>

      <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                  const events = new Array();

                  // Événements des matières (cours)
                  @foreach ($getMyTimetable as $value)
                        @foreach ($value['weeks'] as $week)
                              <?php
                              // Utilise un format standard pour FullCalendar
                              $startTime = date('H:i:s', strtotime($week['start_time']));
                              $endTime = date('H:i:s', strtotime($week['end_time']));
                              ?>
                              events.push({
                                    // Titre avec nom de la matière et plage horaire
                                    title: '{{ $value['name'] }}',
                                    daysOfWeek: [{{ $week['day'] }}],
                                    startTime: '{{ $startTime }}',
                                    endTime: '{{ $endTime }}',
                                    color: '#7c3aed',
                                    url: '{{ url('student/my_exam_timetable') }}',
                                    // Ajout d'une propriété custom pour l'affichage
                                    displayTime: '{{ date('H\hi', strtotime($week['start_time'])) }} - {{ date('H\hi', strtotime($week['end_time'])) }}'
                              });
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
                                    // Titre simplifié pour une meilleure lisibilité
                                    title: '{{ $valueExam['name'] }} ({{ $exam['subject_name'] }})',
                                    start: '{{ $exam['exam_date'] }}T{{ $startTime }}', // Combinaison date + heure
                                    end: '{{ $exam['exam_date'] }}T{{ $endTime }}', // Combinaison date + heure
                                    color: '#34d399',
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
                              right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' // listWeek est plus pertinent que listMonth
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
                              hour12: false // Force le format 24h
                        },
                        events: events,
                        initialView: 'timeGridWeek', // Affichage initial par semaine, plus pertinent pour des emplois du temps
                        // Ajout du callback pour personnaliser le contenu des événements
                        eventContent: function(arg) {
                              let html = `<b>${arg.event.title}</b>`;
                              if (arg.event.extendedProps.displayTime) {
                                    html +=
                                          `<br><span>${arg.event.extendedProps.displayTime}</span>`;
                              } else if (arg.event.start) {
                                    // Utilise le format 24h pour l'affichage des examens
                                    const start = new Date(arg.event.start);
                                    const end = new Date(arg.event.end);
                                    const formattedStart =
                                          `${String(start.getHours()).padStart(2, '0')}h${String(start.getMinutes()).padStart(2, '0')}`;
                                    const formattedEnd =
                                          `${String(end.getHours()).padStart(2, '0')}h${String(end.getMinutes()).padStart(2, '0')}`;
                                    html +=
                                          `<br><span>${formattedStart} - ${formattedEnd}</span>`;
                              }

                              return {
                                    html: html
                              };
                        }
                  });
                  calendar.render();
            });
      </script>
@endsection
