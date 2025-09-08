<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>{{ !empty($header_title) ? $header_title : '' }} - SMS</title>

      @php
            $getSetting = \App\Models\SettingModel::getSingle(1);
            $favicon_url = !empty($getSetting->favicon)
                ? \App\Models\SettingModel::getFavicon()
                : asset('upload/favicon.png');
            $logo_url = !empty($getSetting->logo) ? \App\Models\SettingModel::getLogo() : asset('upload/logo.png');
            $student_photo = !empty($getStudent->profile_picture)
                ? 'upload/profile/' . $getStudent->profile_picture
                : 'upload/default.jpg';
            $school_name = !empty($getSetting->school_name) ? $getSetting->school_name : 'SCHOOL MANAGEMENT SYSTEM';
            $school_address = !empty($getSetting->address) ? $getSetting->address : 'ABOMEY-CALAVI / WOMEY';
            $school_phone = !empty($getSetting->phone) ? $getSetting->phone : '0777777777';
      @endphp

      <style>
            @media print {
                  @page {
                        size: A4 portrait;
                        margin: 1cm;
                  }

                  body {
                        font-family: 'Arial', sans-serif;
                        color: #000;
                        background: #fff;
                        font-size: 12px;
                        line-height: 1.4;
                  }

                  .no-print {
                        display: none !important;
                  }

                  .print-container {
                        width: 100%;
                        max-width: 100%;
                  }

                  table {
                        width: 100%;
                        border-collapse: collapse;
                  }

                  th,
                  td {
                        padding: 6px;
                        border: 1px solid #ddd;
                  }

                  th {
                        background-color: #f5f5f5 !important;
                        color: #000 !important;
                        font-weight: bold;
                        text-align: center;
                  }
            }

            body {
                  font-family: 'Arial', sans-serif;
                  margin: 0;
                  padding: 20px;
                  color: #333;
                  background: #fff;
            }

            .print-container {
                  max-width: 800px;
                  margin: 0 auto;
                  border: 1px solid #ddd;
                  padding: 20px;
                  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .header {
                  display: flex;
                  justify-content: space-between;
                  align-items: center;
                  margin-bottom: 20px;
                  padding-bottom: 15px;
                  border-bottom: 2px solid #4f46e5;
            }

            .school-info {
                  text-align: left;
            }

            .school-name {
                  font-size: 20px;
                  font-weight: bold;
                  color: #4f46e5;
                  margin: 0;
            }

            .school-details {
                  font-size: 12px;
                  margin: 5px 0 0 0;
                  color: #666;
            }

            .logo {
                  max-width: 100px;
                  max-height: 100px;
            }

            .student-info {
                  display: grid;
                  grid-template-columns: 1fr 2fr;
                  gap: 15px;
                  margin-bottom: 20px;
            }

            .student-photo {
                  width: 120px;
                  height: 150px;
                  border: 1px solid #ddd;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  background: #f9f9f9;
            }

            .student-details {
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  gap: 10px;
            }

            .detail-item {
                  margin-bottom: 8px;
            }

            .detail-label {
                  font-weight: bold;
                  color: #555;
            }

            .exam-title {
                  text-align: center;
                  font-size: 18px;
                  font-weight: bold;
                  margin: 20px 0;
                  padding: 10px;
                  background-color: #4f46e5;
                  color: white;
                  border-radius: 4px;
            }

            table {
                  width: 100%;
                  border-collapse: collapse;
                  margin-bottom: 20px;
            }

            th {
                  background-color: #4f46e5;
                  color: white;
                  padding: 10px;
                  text-align: left;
                  font-weight: bold;
            }

            td {
                  padding: 8px 10px;
                  border-bottom: 1px solid #ddd;
            }

            tr:nth-child(even) {
                  background-color: #f9f9f9;
            }

            .summary {
                  margin-top: 20px;
                  padding: 15px;
                  background-color: #f0f4ff;
                  border-radius: 5px;
                  border-left: 4px solid #4f46e5;
            }

            .summary-item {
                  display: flex;
                  justify-content: space-between;
                  margin-bottom: 8px;
            }

            .summary-label {
                  font-weight: bold;
            }

            .result-validated {
                  color: #10b981;
                  font-weight: bold;
            }

            .result-failed {
                  color: #ef4444;
                  font-weight: bold;
            }

            .footer {
                  margin-top: 30px;
                  text-align: center;
                  font-size: 11px;
                  color: #666;
                  border-top: 1px solid #ddd;
                  padding-top: 15px;
            }

            .signature-area {
                  display: flex;
                  justify-content: space-around;
                  margin-top: 40px;
            }

            .signature {
                  text-align: center;
                  width: 40%;
                  border-top: 1px solid #000;
                  padding-top: 40px;
            }

            .print-button {
                  text-align: center;
                  margin: 20px 0;
            }

            .btn-print {
                  background-color: #4f46e5;
                  color: white;
                  border: none;
                  padding: 10px 20px;
                  border-radius: 4px;
                  cursor: pointer;
                  font-size: 16px;
            }
      </style>
</head>

<body>
      <div class="print-container">
            <!-- En-tête avec logo et informations de l'école -->
            <div class="header">
                  <div class="school-info">
                        <h1 class="school-name">{{ $school_name }}</h1>
                        <p class="school-details">{{ $school_address }}</p>
                        <p class="school-details">Tél: {{ $school_phone }}</p>
                  </div>
                  <div>
                        <img src="{{ $logo_url }}" alt="Logo de l'école" class="logo">
                  </div>
            </div>

            <!-- Informations de l'étudiant -->
            <div class="student-info">
                  <div class="student-photo">
                        <img src="{{ asset($student_photo) }}" alt="Photo étudiant"
                              style="max-width: 100%; max-height: 100%;">
                  </div>
                  <div class="student-details">
                        <div class="detail-item">
                              <span class="detail-label">Nom: </span>
                              {{ $getStudent->name }} {{ $getStudent->last_name }}
                        </div>
                        <div class="detail-item">
                              <span class="detail-label">Classe: </span>
                              {{ !empty($getStudent->class_name) ? $getStudent->class_name : 'N/A' }}
                        </div>
                        <div class="detail-item">
                              <span class="detail-label">Matricule: </span>
                              {{ $getStudent->admission_number }}
                        </div>
                        <div class="detail-item">
                              <span class="detail-label">Date de naissance: </span>
                              {{ !empty($getStudent->date_of_birth) ? date('d/m/Y', strtotime($getStudent->date_of_birth)) : 'N/A' }}
                        </div>
                        <div class="detail-item">
                              <span class="detail-label">Année scolaire: </span>
                              2025 - 2026
                        </div>
                        <div class="detail-item">
                              <span class="detail-label">Période: </span>
                              1ère période
                        </div>
                  </div>
            </div>

            <h2 class="exam-title">{{ $exam_name }}</h2>

            <table>
                  <thead>
                        <tr>
                              <th>Matière</th>
                              <th>TC</th>
                              <th>TM</th>
                              <th>TE</th>
                              <th>TEx</th>
                              <th>Note</th>
                              <th>Passage/Total</th>
                              <th>Résultat</th>
                        </tr>
                  </thead>
                  <tbody>
                        @if (empty($getExamResultStudent))
                              <tr>
                                    <td colspan="8" style="text-align: center;">Aucun résultat d'examen trouvé.</td>
                              </tr>
                        @else
                              @foreach ($getExamResultStudent as $subjectValue)
                                    <tr>
                                          <td>{{ $subjectValue['subject_name'] }}</td>
                                          <td style="text-align: center;">{{ $subjectValue['class_work'] }}</td>
                                          <td style="text-align: center;">{{ $subjectValue['home_work'] }}</td>
                                          <td style="text-align: center;">{{ $subjectValue['test_work'] }}</td>
                                          <td style="text-align: center;">{{ $subjectValue['exam_work'] }}</td>
                                          <td style="text-align: center; font-weight: bold;">
                                                {{ $subjectValue['score_marks'] }}</td>
                                          <td style="text-align: center;">
                                                {{ $subjectValue['passing_marks'] }}/{{ $subjectValue['full_marks'] }}
                                          </td>
                                          <td style="text-align: center;">
                                                @if ($subjectValue['score_marks'] >= $subjectValue['passing_marks'])
                                                      <span class="result-validated">Validé</span>
                                                @else
                                                      <span class="result-failed">Non validé</span>
                                                @endif
                                          </td>
                                    </tr>
                              @endforeach

                              <!-- Ligne de total -->
                              <tr style="background-color: #f0f4ff; font-weight: bold;">
                                    <td>TOTAL</td>
                                    <td style="text-align: center;">{{ $total_class_work }}</td>
                                    <td style="text-align: center;">{{ $total_home_work }}</td>
                                    <td style="text-align: center;">{{ $total_test_work }}</td>
                                    <td style="text-align: center;">{{ $total_exam_work }}</td>
                                    <td style="text-align: center;">{{ $total_score }}</td>
                                    <td style="text-align: center;">{{ $passing_marks }}/{{ $full_marks }}</td>
                                    <td style="text-align: center;">
                                          @if ($total_score >= $passing_marks)
                                                <span class="result-validated">Validé</span>
                                          @else
                                                <span class="result-failed">Non validé</span>
                                          @endif
                                    </td>
                              </tr>
                        @endif
                  </tbody>
            </table>

            <!-- Résumé des performances -->
            <div class="summary">
                  <div class="summary-item">
                        <span class="summary-label">Pourcentage Total:</span>
                        <span>{{ $percentage }}%</span>
                  </div>
                  <div class="summary-item">
                        <span class="summary-label">Grade:</span>
                        <span>{{ $getGrade }}</span>
                  </div>
                  <div class="summary-item">
                        <span class="summary-label">Appréciation:</span>
                        <span>
                              @if ($percentage >= 80)
                                    Excellent
                              @elseif ($percentage >= 70)
                                    Très bien
                              @elseif ($percentage >= 60)
                                    Bien
                              @elseif ($percentage >= 50)
                                    Assez bien
                              @elseif ($percentage >= 40)
                                    Passable
                              @else
                                    Insuffisant
                              @endif
                        </span>
                  </div>
            </div>

            <!-- Zone de signature -->
            <div class="signature-area">
                  <div class="signature">
                        Le Responsable Pédagogique<br>
                        Nom et Signature
                  </div>
                  <div class="signature">
                        Le Directeur<br>
                        Nom et Signature
                  </div>
            </div>

            <!-- Pied de page -->
            <div class="footer">
                  <p>Bulletin généré le {{ date('d/m/Y à H:i') }} | {{ $school_name }} - Tous droits réservés</p>
            </div>
      </div>

      <script>
            window.onload = function() {
                  window.print();
            }
      </script>
</body>

</html>
