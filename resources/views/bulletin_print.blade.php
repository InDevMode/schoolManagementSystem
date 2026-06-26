<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Bulletin — {{ $detail['bulletin']->student_last_name }} {{ $detail['bulletin']->student_name }}</title>
    <style>
        @page { size: A4 portrait; margin: 0.8cm; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Arial', 'Helvetica Neue', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Wrapper principal ─────────────────────────────────────── */
        .bulletin {
            width: 100%;
            max-width: 740px;
            margin: 0 auto;
            border: 2px solid #1a1a2e;
        }

        /* ── Bandeau drapeau Bénin ────────────────────────────────── */
        .flag-bar {
            height: 6px;
            background: linear-gradient(to right, #008751 33.3%, #ffd600 33.3% 66.6%, #ef2b2d 66.6%);
        }

        /* ── En-tête ─────────────────────────────────────────────── */
        .header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #f8f9ff;
            border-bottom: 1px solid #d1d5db;
        }

        .header-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .header-logo-placeholder {
            width: 60px;
            height: 60px;
            background: #e8eeff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .header-center {
            flex: 1;
            text-align: center;
        }

        .header-republic {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #374151;
        }

        .header-ministry {
            font-size: 8px;
            color: #6b7280;
            margin-top: 1px;
        }

        .header-school {
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            color: #1a1a2e;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        .header-motto {
            font-size: 9px;
            color: #6b7280;
            font-style: italic;
            margin-top: 2px;
        }

        .header-bulletin-type {
            margin-top: 6px;
            display: inline-block;
            background: #4f46e5;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 14px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        .header-photo {
            width: 70px;
            flex-shrink: 0;
        }

        .header-photo img,
        .header-photo .photo-placeholder {
            width: 70px;
            height: 85px;
            object-fit: cover;
            border: 2px solid #d1d5db;
            border-radius: 4px;
        }

        .header-photo .photo-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            font-size: 28px;
            color: #9ca3af;
        }

        /* ── Infos apprenant ──────────────────────────────────────────── */
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            padding: 10px 16px;
            background: #fff;
            border-bottom: 1px solid #d1d5db;
        }

        .info-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            padding: 2px 0;
        }

        .info-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            min-width: 90px;
            flex-shrink: 0;
        }

        .info-value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        /* ── Tableau des matières ─────────────────────────────────── */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .grades-table thead tr {
            background: #4f46e5;
            color: #fff;
        }

        .grades-table thead th {
            padding: 7px 10px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.2);
        }

        .grades-table thead th:first-child { text-align: left; }
        .grades-table thead th:last-child  { border-right: none; }

        .grades-table tbody tr:nth-child(even)  { background: #f9fafb; }
        .grades-table tbody tr:nth-child(odd)   { background: #ffffff; }
        .grades-table tbody tr:last-child td    { border-bottom: none; }

        .grades-table tbody td {
            padding: 6px 10px;
            font-size: 10.5px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            text-align: center;
        }

        .grades-table tbody td:first-child  { text-align: left; font-weight: 600; color: #111827; }
        .grades-table tbody td:last-child   { border-right: none; font-style: italic; color: #374151; text-align: left; }

        .grades-table tfoot tr { background: #f0f4ff; }

        .grades-table tfoot td {
            padding: 6px 10px;
            font-size: 10px;
            font-weight: 700;
            border-right: 1px solid #d1d5db;
            text-align: center;
            color: #1a1a2e;
        }

        .grades-table tfoot td:first-child { text-align: left; }
        .grades-table tfoot td:last-child  { border-right: none; }

        /* Couleurs de note */
        .avg-excellent { color: #059669; font-weight: 700; }
        .avg-bien      { color: #0891b2; font-weight: 700; }
        .avg-passable  { color: #d97706; font-weight: 700; }
        .avg-faible    { color: #dc2626; font-weight: 700; }

        /* ── Résumé général ───────────────────────────────────────── */
        .summary-bar {
            background: #4f46e5;
            color: #fff;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            text-align: center;
            padding: 10px 0;
            gap: 0;
        }

        .summary-item {
            padding: 4px 8px;
            border-right: 1px solid rgba(255,255,255,0.2);
        }

        .summary-item:last-child { border-right: none; }

        .summary-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .summary-value {
            font-size: 17px;
            font-weight: 900;
            margin-top: 2px;
        }

        .summary-value.large { font-size: 20px; }

        /* ── Commentaire / Appréciation ───────────────────────────── */
        .appreciation-section {
            padding: 10px 16px;
            border-top: 1px solid #e5e7eb;
        }

        .appreciation-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .appreciation-text {
            font-size: 10.5px;
            font-style: italic;
            color: #374151;
            border-left: 3px solid #4f46e5;
            padding-left: 8px;
            min-height: 22px;
        }

        /* ── Signatures ───────────────────────────────────────────── */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            padding: 16px 24px 12px;
            border-top: 1px solid #e5e7eb;
        }

        .signature-block {
            text-align: center;
        }

        .signature-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #374151;
            margin-bottom: 32px;
        }

        .signature-line {
            border-top: 1px solid #374151;
            padding-top: 4px;
            font-size: 9px;
            color: #6b7280;
        }

        /* ── Pied de page ─────────────────────────────────────────── */
        .footer {
            padding: 6px 16px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }

        /* ── Bouton impression (masqué à l'impression) ────────────── */
        .print-btn {
            display: block;
            text-align: center;
            margin: 20px auto;
        }

        .btn {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 600;
        }

        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

@php
    $bulletin  = $detail['bulletin'];
    $subjects  = $detail['subjects'];
    $settings  = $settings ?? null;

    $schoolName = $settings?->school_name ?? 'École';
    $logoUrl    = $settings ? $settings->getLogo() : asset('upload/logo.png');

    $studentPhoto = !empty($bulletin->profile_picture)
        ? asset('upload/profile/' . $bulletin->profile_picture)
        : null;

    $periodLabel = $bulletin->period_type === 'semestre'
        ? 'Semestre ' . $bulletin->order_number
        : 'Trimestre ' . $bulletin->order_number;

    $bulletinType = $bulletin->period_type === 'semestre'
        ? 'BULLETIN SEMESTRIEL'
        : 'BULLETIN TRIMESTRIEL';

    // Calculs totaux
    $totalCoeff   = collect($subjects)->sum('coefficient');
    $totalPoints  = collect($subjects)->sum('weighted_points');

    // Fonction appréciation avec couleur
    $avgColor = function(float $avg): string {
        if ($avg >= 14) return 'avg-excellent';
        if ($avg >= 10) return 'avg-bien';
        if ($avg >= 8)  return 'avg-passable';
        return 'avg-faible';
    };
@endphp

<!-- Bouton imprimer -->
<div class="print-btn">
    <button class="btn" onclick="window.print()">🖨️ Imprimer le bulletin</button>
</div>

<div class="bulletin">
    <!-- Bandeau drapeau -->
    <div class="flag-bar"></div>

    <!-- En-tête -->
    <div class="header">
        <div class="header-logo">
            <img src="{{ $logoUrl }}" alt="Logo"/>
        </div>
        <div class="header-center">
            <div class="header-republic">République du Bénin</div>
            <div class="header-ministry">Ministère des Enseignements Secondaire, Technique et de la Formation Professionnelle</div>
            <div class="header-school">{{ $schoolName }}</div>
            <div class="header-motto">Travail — Discipline — Réussite</div>
            <span class="header-bulletin-type">{{ $bulletinType }} — {{ $bulletin->school_year ?? '' }}</span>
        </div>
        <div class="header-photo">
            @if($studentPhoto)
                <img src="{{ $studentPhoto }}" alt="Photo apprenant"/>
            @else
                <div class="photo-placeholder">👤</div>
            @endif
        </div>
    </div>

    <!-- Infos apprenant -->
    <div class="student-info">
        <div class="info-row">
            <span class="info-label">Nom de l'apprenant :</span>
            <span class="info-value">{{ $bulletin->student_last_name }} {{ $bulletin->student_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Classe :</span>
            <span class="info-value">{{ $bulletin->class_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Matricule :</span>
            <span class="info-value">{{ $bulletin->admission_number ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ $bulletin->period_type === 'semestre' ? 'Semestre' : 'Trimestre' }} :</span>
            <span class="info-value">{{ $bulletin->order_number }}{{ $bulletin->order_number == 1 ? 'ᵉʳ' : 'ᵉ' }} {{ $bulletin->period_type === 'semestre' ? 'Semestre' : 'Trimestre' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de naissance :</span>
            <span class="info-value">{{ !empty($bulletin->date_of_birth) ? \Carbon\Carbon::parse($bulletin->date_of_birth)->format('d-m-Y') : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Année scolaire :</span>
            <span class="info-value">{{ $bulletin->school_year ?? '—' }}</span>
        </div>
    </div>

    <!-- Tableau matières -->
    <table class="grades-table">
        <thead>
            <tr>
                <th style="width:30%; text-align:left">Matières</th>
                <th>Notes</th>
                <th>Coef.</th>
                <th>Moyenne</th>
                <th style="width:25%; text-align:left">Appréciations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjects as $sub)
                @php
                    $avg = round($sub->average ?? 0, 2);
                    $colorClass = $avgColor($avg);
                @endphp
                <tr>
                    <td>{{ $sub->subject_name }}</td>
                    <td>{{ $avg > 0 ? number_format($avg, 2) . ' / 20' : '—' }}</td>
                    <td><strong>{{ $sub->coefficient }}</strong></td>
                    <td><span class="{{ $colorClass }}">{{ $avg > 0 ? number_format($avg, 2) : '—' }}</span></td>
                    <td>{{ $sub->appreciation ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#9ca3af; padding:16px">
                        Aucune matière enregistrée pour cette période.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>Total des Coefficients</td>
                <td></td>
                <td style="color:#4f46e5; font-size:12px">{{ $totalCoeff }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Total des Points</td>
                <td></td>
                <td></td>
                <td style="color:#4f46e5; font-size:12px">{{ number_format($totalPoints, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Résumé barre violette -->
    <div class="summary-bar">
        <div class="summary-item">
            <div class="summary-label">Moyenne Générale</div>
            <div class="summary-value large">
                {{ $bulletin->average ? number_format($bulletin->average, 2) : '—' }} / 20
            </div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Rang</div>
            <div class="summary-value">
                @if($bulletin->rank)
                    {{ $bulletin->rank }}{{ $bulletin->rank == 1 ? 'ᵉʳ' : 'ᵉ' }} / {{ $bulletin->total_students }}
                @else
                    —
                @endif
            </div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Taux de réussite</div>
            <div class="summary-value">
                {{ $bulletin->class_success_rate ? $bulletin->class_success_rate . '%' : '—' }}
            </div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Appréciation</div>
            <div class="summary-value" style="font-size:14px; padding-top:4px">
                {{ $bulletin->appreciation ?? '—' }}
            </div>
        </div>
    </div>

    <!-- Appréciation générale (commentaire prof) -->
    <div class="appreciation-section">
        <div class="appreciation-label">Appréciation générale du Professeur Principal</div>
        <div class="appreciation-text">
            {{ $bulletin->teacher_comment ?? 'Aucune appréciation saisie.' }}
        </div>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-block">
            <div class="signature-title">Le Professeur Principal</div>
            <div class="signature-line">Nom et Signature</div>
        </div>
        <div class="signature-block">
            <div class="signature-title">Le Directeur</div>
            <div class="signature-line">Nom et Signature</div>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        Bulletin généré le {{ now()->format('d-m-Y à H:i') }} — {{ $schoolName }} — Tous droits réservés
    </div>

    <!-- Bandeau drapeau bas -->
    <div class="flag-bar"></div>
</div>

<script>
    // Auto-impression après chargement
    window.addEventListener('load', () => {
        // Petit délai pour laisser les images se charger
        setTimeout(() => window.print(), 800);
    });
</script>
</body>
</html>
