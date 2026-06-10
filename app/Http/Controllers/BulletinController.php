<?php

namespace App\Http\Controllers;

use App\Models\BulletinModel;
use App\Models\ClassModel;
use App\Models\DeletionLogModel;
use App\Models\PeriodModel;
use App\Models\SettingModel;
use App\Models\User;
use App\Notifications\BulletinPublishedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BulletinController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // ADMIN — Gestion des bulletins
    // ──────────────────────────────────────────────────────────────────────────

    public function list()
    {
        return Inertia::render('Admin/Bulletins/Index', [
            'bulletins' => BulletinModel::getAll(15),
            'classes'   => ClassModel::getClass(),
            'periods'   => PeriodModel::getAllPeriods(),
        ]);
    }

    /**
     * Génère le bulletin d'un seul élève
     */
    public function generate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'period_id'  => 'required|integer',
        ]);

        try {
            $bulletin = BulletinModel::generate(
                (int) $request->student_id,
                (int) $request->period_id,
                Auth::id()
            );

            return redirect()->back()->with('success', 'Bulletin généré avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur génération bulletin : " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Génère les bulletins de toute une classe en masse
     */
    public function generateForClass(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|integer',
            'period_id' => 'required|integer',
        ]);

        try {
            $results = BulletinModel::generateForClass(
                (int) $request->class_id,
                (int) $request->period_id,
                Auth::id()
            );

            $msg = "{$results['success']} bulletin(s) généré(s) avec succès.";
            if (!empty($results['errors'])) {
                $msg .= " " . count($results['errors']) . " erreur(s).";
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            Log::error("Erreur génération bulletins classe : " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Publier un bulletin (le rend visible aux élèves/parents)
     */
    public function publish(int $id)
    {
        $bulletin = BulletinModel::getSingle($id);
        if (!$bulletin) abort(404);

        $bulletin->status = 'published';
        $bulletin->save();

        // Notifier l'élève et son parent
        try {
            $student = User::find($bulletin->student_id);
            $period  = PeriodModel::find($bulletin->period_id);
            $periodName = $period?->name ?? 'Période inconnue';

            if ($student) {
                $studentName = "{$student->name} {$student->last_name}";

                // Notification à l'élève
                $student->notify(new BulletinPublishedNotification($studentName, $periodName, $id, 'student'));

                // Notification au parent s'il existe
                if ($student->parent_id) {
                    $parent = User::find($student->parent_id);
                    $parent?->notify(new BulletinPublishedNotification($studentName, $periodName, $id, 'parent'));
                }
            }
        } catch (\Exception $e) {
            Log::warning("Bulletin publish notification failed for id #{$id}: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Bulletin publié avec succès.');
    }

    /**
     * Publier tous les bulletins d'une classe/période
     */
    public function publishAll(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|integer',
            'period_id' => 'required|integer',
        ]);

        try {
            $bulletinsQuery = BulletinModel::select('bulletins.*')
                ->join('users', 'users.id', '=', 'bulletins.student_id')
                ->where('users.class_id', $request->class_id)
                ->where('bulletins.period_id', $request->period_id)
                ->where('bulletins.is_delete', 0)
                ->where('bulletins.status', 'draft');

            $bulletins = $bulletinsQuery->get();
            $count = $bulletins->count();

            // Mise à jour en base
            BulletinModel::select('bulletins.id')
                ->join('users', 'users.id', '=', 'bulletins.student_id')
                ->where('users.class_id', $request->class_id)
                ->where('bulletins.period_id', $request->period_id)
                ->where('bulletins.is_delete', 0)
                ->where('bulletins.status', 'draft')
                ->update(['bulletins.status' => 'published']);

            // Notifier chaque élève et son parent
            try {
                $period = PeriodModel::find($request->period_id);
                $periodName = $period?->name ?? 'Période inconnue';

                foreach ($bulletins as $bulletin) {
                    $student = User::find($bulletin->student_id);
                    if (!$student) continue;

                    $studentName = "{$student->name} {$student->last_name}";
                    $student->notify(new BulletinPublishedNotification($studentName, $periodName, $bulletin->id, 'student'));

                    if ($student->parent_id) {
                        $parent = User::find($student->parent_id);
                        $parent?->notify(new BulletinPublishedNotification($studentName, $periodName, $bulletin->id, 'parent'));
                    }
                }
            } catch (\Exception $notifEx) {
                Log::warning("PublishAll notification failed: " . $notifEx->getMessage());
            }

            return redirect()->back()->with('success', "{$count} bulletin(s) publié(s) avec succès.");
        } catch (\Exception $e) {
            Log::error("Erreur publication bulletins : " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la publication.');
        }
    }

    /**
     * Mettre à jour le commentaire du prof principal
     */
    public function updateComment(Request $request, int $id)
    {
        $request->validate(['teacher_comment' => 'nullable|string|max:1000']);

        $bulletin = BulletinModel::getSingle($id);
        if (!$bulletin) abort(404);

        $bulletin->teacher_comment = $request->teacher_comment;
        $bulletin->save();

        return response()->json(['success' => true, 'message' => 'Commentaire enregistré.']);
    }

    /**
     * Supprimer un bulletin (soft delete + log)
     */
    public function delete(int $id)
    {
        $bulletin = BulletinModel::getSingle($id);
        if (!$bulletin) abort(404);

        DeletionLogModel::log('bulletins', $bulletin->id, $bulletin->toArray());
        $bulletin->is_delete = 1;
        $bulletin->save();

        return redirect()->back()->with('success', 'Bulletin supprimé.');
    }

    /**
     * Vue détaillée d'un bulletin (admin)
     */
    public function show(int $id)
    {
        $detail = BulletinModel::getFullDetail($id);
        if (empty($detail)) abort(404);

        return Inertia::render('Admin/Bulletins/Show', [
            'detail'   => $detail,
            'settings' => SettingModel::getSingle(1),
        ]);
    }

    /**
     * Impression du bulletin — retourne une vue Blade pour l'impression
     */
    public function print(int $id)
    {
        $detail = BulletinModel::getFullDetail($id);
        if (empty($detail)) abort(404);

        $settings = SettingModel::getSingle(1);

        return view('bulletin_print', [
            'detail'   => $detail,
            'settings' => $settings,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ÉLÈVE — Ses bulletins
    // ──────────────────────────────────────────────────────────────────────────

    public function studentBulletins()
    {
        $bulletins = BulletinModel::getByStudent(Auth::id());

        return Inertia::render('Student/Bulletins/Index', [
            'bulletins' => $bulletins,
        ]);
    }

    public function studentBulletinShow(int $id)
    {
        $bulletin = BulletinModel::getSingle($id);

        // Sécurité : un élève ne peut voir que ses propres bulletins publiés
        if (!$bulletin || $bulletin->student_id !== Auth::id() || $bulletin->status !== 'published') {
            abort(403);
        }

        $detail = BulletinModel::getFullDetail($id);

        return Inertia::render('Student/Bulletins/Show', [
            'detail'   => $detail,
            'settings' => SettingModel::getSingle(1),
        ]);
    }

    public function studentBulletinPrint(int $id)
    {
        $bulletin = BulletinModel::getSingle($id);
        if (!$bulletin || $bulletin->student_id !== Auth::id() || $bulletin->status !== 'published') {
            abort(403);
        }

        $detail   = BulletinModel::getFullDetail($id);
        $settings = SettingModel::getSingle(1);

        return view('bulletin_print', ['detail' => $detail, 'settings' => $settings]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PARENT — Bulletins de son enfant
    // ──────────────────────────────────────────────────────────────────────────

    public function parentStudentBulletins(int $student_id)
    {
        $student = User::find($student_id);
        if (!$student || $student->parent_id !== Auth::id()) abort(403);

        $bulletins = BulletinModel::getByStudent($student_id);

        return Inertia::render('Parent/Bulletins/Index', [
            'bulletins' => $bulletins,
            'student'   => $student,
        ]);
    }

    public function parentStudentBulletinShow(int $student_id, int $id)
    {
        $student = User::find($student_id);
        if (!$student || $student->parent_id !== Auth::id()) abort(403);

        $bulletin = BulletinModel::getSingle($id);
        if (!$bulletin || $bulletin->student_id !== $student_id || $bulletin->status !== 'published') {
            abort(403);
        }

        $detail   = BulletinModel::getFullDetail($id);
        $settings = SettingModel::getSingle(1);

        return Inertia::render('Parent/Bulletins/Show', [
            'detail'   => $detail,
            'student'  => $student,
            'settings' => $settings,
        ]);
    }

    public function parentStudentBulletinPrint(int $student_id, int $id)
    {
        $student = User::find($student_id);
        if (!$student || $student->parent_id !== Auth::id()) abort(403);

        $bulletin = BulletinModel::getSingle($id);
        if (!$bulletin || $bulletin->student_id !== $student_id || $bulletin->status !== 'published') {
            abort(403);
        }

        $detail   = BulletinModel::getFullDetail($id);
        $settings = SettingModel::getSingle(1);

        return view('bulletin_print', ['detail' => $detail, 'settings' => $settings]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // API JSON — utilitaires
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Aperçu rapide des moyennes d'une classe avant génération (AJAX)
     */
    public function previewClassAverages(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|integer',
            'period_id' => 'required|integer',
        ]);

        $averages = BulletinModel::computeClassAverages(
            (int) $request->class_id,
            (int) $request->period_id
        );

        // Enrichir avec le nom des élèves
        $students = User::whereIn('id', array_keys($averages))
            ->select('id', 'name', 'last_name', 'admission_number')
            ->get()
            ->keyBy('id');

        $result = collect($averages)
            ->map(fn($avg, $id) => [
                'student_id'  => $id,
                'name'        => $students[$id]->name ?? '',
                'last_name'   => $students[$id]->last_name ?? '',
                'average'     => $avg,
                'appreciation'=> BulletinModel::getAppreciation($avg),
            ])
            ->values()
            ->sortByDesc('average')
            ->values();

        return response()->json($result);
    }
}
