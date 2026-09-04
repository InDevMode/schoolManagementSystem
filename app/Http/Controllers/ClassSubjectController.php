<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\SubjectModel;
use App\Services\RefDataCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ClassSubjectController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/AssignSubject/Index', [
            'classSubjects' => ClassSubjectModel::getAllClassSubject(15),
            'classes'       => RefDataCache::classes(),
            'subjects'      => RefDataCache::subjects(),
        ]);
    }

    /**
     * Assigner plusieurs matières à une classe.
     * Si la paire (class_id, subject_id) existe déjà (même supprimée), on met à jour.
     * Sinon on crée. On ne crée jamais deux fois la même paire.
     */
    public function create(Request $request)
    {
        $request->validate([
            'class_id'    => 'required|integer|exists:class,id',
            'subject_id'  => 'required|array|min:1',
            'subject_id.*'=> 'integer|exists:subject,id',
            'coefficient' => 'required|integer|min:1',
            'status'      => 'required|in:0,1',
        ]);

        try {
            $skipped = 0;
            $created = 0;

            foreach ($request->subject_id as $subject_id) {
                // Recherche toutes les entrées (même is_delete=1)
                $existing = ClassSubjectModel::withoutGlobalScopes()
                    ->where('class_id',   $request->class_id)
                    ->where('subject_id', $subject_id)
                    ->first();

                if ($existing) {
                    // Met à jour l'entrée existante (la réactive si supprimée)
                    $existing->status      = $request->status;
                    $existing->coefficient = $request->coefficient;
                    $existing->is_delete   = 0;
                    $existing->save();
                    $skipped++;
                } else {
                    $classSubject             = new ClassSubjectModel();
                    $classSubject->class_id   = $request->class_id;
                    $classSubject->subject_id = $subject_id;
                    $classSubject->status     = $request->status;
                    $classSubject->coefficient = $request->coefficient;
                    $classSubject->created_by = Auth::user()->id;
                    $classSubject->save();
                    $created++;
                }
            }

            $msg = $created > 0
                ? "{$created} matière(s) assignée(s) avec succès."
                : "Les matières sélectionnées étaient déjà assignées et ont été mises à jour.";

            return redirect('admin/assign_subject/list')->with('success', $msg);

        } catch (\Exception $e) {
            Log::error("Erreur assignation matières : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Remplacer toutes les assignations d'une classe.
     * Supprime les paires non présentes dans la nouvelle liste, crée ou met à jour les autres.
     */
    public function update(Request $request)
    {
        $request->validate([
            'class_id'    => 'required|integer|exists:class,id',
            'subject_id'  => 'required|array|min:1',
            'subject_id.*'=> 'integer|exists:subject,id',
            'coefficient' => 'required|integer|min:1',
            'status'      => 'required|in:0,1',
        ]);

        try {
            $classId    = $request->class_id;
            $subjectIds = array_map('intval', $request->subject_id);

            // Soft-delete les assignations qui ne sont plus dans la liste
            ClassSubjectModel::where('class_id', $classId)
                ->where('is_delete', 0)
                ->whereNotIn('subject_id', $subjectIds)
                ->update(['is_delete' => 1]);

            foreach ($subjectIds as $subject_id) {
                ClassSubjectModel::withoutGlobalScopes()
                    ->updateOrCreate(
                        ['class_id' => $classId, 'subject_id' => $subject_id],
                        [
                            'status'      => $request->status,
                            'coefficient' => $request->coefficient,
                            'is_delete'   => 0,
                            'created_by'  => Auth::user()->id,
                        ]
                    );
            }

            return redirect('admin/assign_subject/list')
                ->with('success', 'Les assignations ont été mises à jour avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur mise à jour assignations : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Modifier une seule assignation (classe + matière + statut + coefficient).
     * Bloque si la nouvelle paire (class_id, subject_id) existe déjà sur un autre enregistrement.
     */
    public function updateSingle(Request $request, $id)
    {
        $request->validate([
            'class_id'    => 'required|integer|exists:class,id',
            'subject_id'  => 'required|integer|exists:subject,id',
            'coefficient' => 'required|integer|min:1',
            'status'      => 'required|in:0,1',
        ]);

        try {
            $classSubject = ClassSubjectModel::getSingle($id);
            if (!$classSubject) abort(404);

            // Vérifier qu'une autre ligne n'a pas déjà la même paire
            $conflict = ClassSubjectModel::where('class_id',   $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->where('id',         '!=', $id)
                ->where('is_delete',  0)
                ->first();

            if ($conflict) {
                return redirect()->back()->with(
                    'error',
                    'Cette matière est déjà assignée à cette classe. Veuillez choisir une combinaison différente.'
                );
            }

            $classSubject->class_id    = $request->class_id;
            $classSubject->subject_id  = $request->subject_id;
            $classSubject->status      = $request->status;
            $classSubject->coefficient = $request->coefficient;
            $classSubject->save();

            return redirect('admin/assign_subject/list')
                ->with('success', 'L\'assignation a été modifiée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur modification assignation : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function delete($id)
    {
        $classSubject = ClassSubjectModel::getSingle($id);
        if ($classSubject) {
            $classSubject->is_delete = 1;
            $classSubject->save();
            return redirect('admin/assign_subject/list')
                ->with('success', 'Cette assignation a été supprimée avec succès.');
        } else {
            abort(404);
        }
    }
}
