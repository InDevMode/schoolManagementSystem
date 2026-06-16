<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

/**
 * Gestion des écoles — accessible au super admin uniquement.
 * Chaque école est une entité multi-tenant avec ses propres paramètres.
 */
class SchoolController extends Controller
{
    // ── Liste ──────────────────────────────────────────────────────────────

    public function list()
    {
        $perPage = min((int) request('per_page', 15), 100);

        $query = School::query()->where('is_delete', 0);

        if ($name = request('name')) {
            $query->where('school_name', 'like', '%' . $name . '%');
        }
        if ($code = request('code')) {
            $query->where('school_code', 'like', '%' . $code . '%');
        }
        if (in_array(request('status'), ['0', '1'], true)) {
            $query->where('status', request('status'));
        }

        $schools = $query->withCount([
            'users as total_users',
            'admins as total_admins',
        ])->orderBy('id', 'desc')->paginate($perPage);

        return Inertia::render('SuperAdmin/Schools/Index', [
            'schools' => $schools,
        ]);
    }

    // ── Créer ──────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:150',
            'school_type' => 'nullable|string|max:100',
            'address'     => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'uai_number'  => 'nullable|string|max:50',
            'status'      => 'required|in:0,1',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'favicon'     => 'nullable|image|mimes:jpg,jpeg,png,ico,webp|max:512',
        ]);

        try {
            $school = new School();

            $school->school_name = trim($request->school_name);
            $school->school_type = $request->school_type ? trim($request->school_type) : null;
            $school->school_code = School::generateCode($request->school_name);
            $school->address     = $request->address ? trim($request->address) : null;
            $school->phone       = $request->phone ? trim($request->phone) : null;
            $school->email       = $request->email ? trim($request->email) : null;
            $school->uai_number  = $request->uai_number ? trim($request->uai_number) : null;
            $school->status      = $request->status;
            $school->created_by  = Auth::id();

            if ($request->hasFile('logo')) {
                $school->logo = $this->uploadFile($request, 'logo');
            }
            if ($request->hasFile('favicon')) {
                $school->favicon = $this->uploadFile($request, 'favicon');
            }

            $school->save();

            return back()->with('success', "École « {$school->school_name} » créée avec succès. Code : {$school->school_code}");
        } catch (\Exception $e) {
            Log::error('SchoolController::create — ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création de l\'école.');
        }
    }

    // ── Modifier ───────────────────────────────────────────────────────────

    public function update(Request $request, int $id)
    {
        $school = School::findOrFail($id);

        $request->validate([
            'school_name' => 'required|string|max:150',
            'school_type' => 'nullable|string|max:100',
            'address'     => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'uai_number'  => 'nullable|string|max:50',
            'status'      => 'required|in:0,1',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'favicon'     => 'nullable|image|mimes:jpg,jpeg,png,ico,webp|max:512',
        ]);

        try {
            $school->school_name = trim($request->school_name);
            $school->school_type = $request->school_type ? trim($request->school_type) : null;
            $school->address     = $request->address ? trim($request->address) : null;
            $school->phone       = $request->phone ? trim($request->phone) : null;
            $school->email       = $request->email ? trim($request->email) : null;
            $school->uai_number  = $request->uai_number ? trim($request->uai_number) : null;
            $school->status      = $request->status;

            if ($request->hasFile('logo')) {
                $this->deleteFile($school->logo);
                $school->logo = $this->uploadFile($request, 'logo');
            }
            if ($request->hasFile('favicon')) {
                $this->deleteFile($school->favicon);
                $school->favicon = $this->uploadFile($request, 'favicon');
            }

            $school->save();

            return back()->with('success', "École « {$school->school_name} » modifiée avec succès.");
        } catch (\Exception $e) {
            Log::error('SchoolController::update — ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la modification de l\'école.');
        }
    }

    // ── Supprimer (soft delete) ────────────────────────────────────────────

    public function delete(int $id)
    {
        $school = School::findOrFail($id);

        // Vérifier qu'il n'y a pas d'utilisateurs actifs dans cette école
        $activeUsers = User::where('school_id', $id)
            ->where('is_delete', 0)
            ->where('user_type', '!=', 0)
            ->count();

        if ($activeUsers > 0) {
            return back()->with('error', "Impossible de supprimer cette école : {$activeUsers} utilisateur(s) actif(s) y sont rattachés.");
        }

        try {
            $school->update(['is_delete' => 1]);
            return back()->with('success', "École « {$school->school_name} » supprimée.");
        } catch (\Exception $e) {
            Log::error('SchoolController::delete — ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    // ── Paramètres d'une école (accessible à l'admin de l'école) ──────────

    /**
     * Page paramètres de l'école — pour les admins (et non le super admin).
     * Le super admin est redirigé vers ses propres paramètres.
     */
    public function settings()
    {
        $user = Auth::user();

        // Bloquer le super admin — il n'appartient pas à une école
        if ($user->user_type === 0) {
            return redirect()->route('superadmin.settings')
                ->with('error', 'Le super admin n\'a pas accès aux paramètres d\'une école. Utilisez la configuration globale.');
        }

        $school = School::find($user->school_id);

        if (! $school) {
            return redirect()->route('dashboard.default')
                ->with('error', 'Vous n\'êtes rattaché à aucune école. Contactez le super administrateur.');
        }

        return Inertia::render('Admin/Settings/Index', [
            'setting'    => $school,
            'faviconUrl' => $school->getFaviconUrl(),
            'logoUrl'    => $school->getLogoUrl(),
            'isSchool'   => true,
        ]);
    }

    /**
     * Sauvegarde des paramètres de l'école par l'admin.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        if ($user->user_type === 0) {
            return back()->with('error', 'Action non autorisée.');
        }

        $school = School::find($user->school_id);

        if (! $school) {
            return back()->with('error', 'École introuvable.');
        }

        $request->validate([
            'school_name'        => 'required|string|max:150',
            'school_type'        => 'nullable|string|max:100',
            'address'            => 'nullable|string|max:255',
            'phone'              => 'nullable|string|max:30',
            'email'              => 'nullable|email|max:150',
            'uai_number'         => 'nullable|string|max:50',
            'status'             => 'nullable|in:0,1',
            'paypal_email'       => 'nullable|email|max:150',
            'kkiapay_public_key'  => 'nullable|string|max:255',
            'kkiapay_private_key' => 'nullable|string|max:255',
            'kkiapay_secret_key'  => 'nullable|string|max:255',
            'stripe_public_key'  => 'nullable|string|max:255',
            'stripe_secret_key'  => 'nullable|string|max:255',
            'fedapay_public_key' => 'nullable|string|max:255',
            'fedapay_secret_key' => 'nullable|string|max:255',
            'logo'               => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'favicon'            => 'nullable|image|mimes:jpg,jpeg,png,ico,webp|max:512',
        ]);

        try {
            $school->school_name        = trim($request->school_name);
            $school->school_type        = $request->school_type ? trim($request->school_type) : null;
            $school->address            = $request->address ? trim($request->address) : null;
            $school->phone              = $request->phone ? trim($request->phone) : null;
            $school->email              = $request->email ? trim($request->email) : null;
            $school->uai_number         = $request->uai_number ? trim($request->uai_number) : null;
            $school->paypal_email       = $request->paypal_email ? trim($request->paypal_email) : null;
            $school->kkiapay_public_key  = $request->kkiapay_public_key ? trim($request->kkiapay_public_key) : null;
            $school->kkiapay_private_key = $request->kkiapay_private_key ? trim($request->kkiapay_private_key) : null;
            $school->kkiapay_secret_key  = $request->kkiapay_secret_key ? trim($request->kkiapay_secret_key) : null;
            $school->stripe_public_key  = $request->stripe_public_key ? trim($request->stripe_public_key) : null;
            $school->stripe_secret_key  = $request->stripe_secret_key ? trim($request->stripe_secret_key) : null;
            $school->fedapay_public_key = $request->fedapay_public_key ? trim($request->fedapay_public_key) : null;
            $school->fedapay_secret_key = $request->fedapay_secret_key ? trim($request->fedapay_secret_key) : null;

            if ($request->filled('status')) {
                $school->status = $request->status;
            }

            if ($request->hasFile('logo')) {
                $this->deleteFile($school->logo);
                $school->logo = $this->uploadFile($request, 'logo');
            }
            if ($request->hasFile('favicon')) {
                $this->deleteFile($school->favicon);
                $school->favicon = $this->uploadFile($request, 'favicon');
            }

            $school->save();

            return back()->with('success', 'Paramètres de l\'école enregistrés avec succès.');
        } catch (\Exception $e) {
            Log::error('SchoolController::updateSettings — ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la sauvegarde des paramètres.');
        }
    }

    // ── Helpers privés ─────────────────────────────────────────────────────

    private function uploadFile(Request $request, string $field): string
    {
        $file     = $request->file($field);
        $ext      = $file->getClientOriginalExtension();
        $fileName = strtolower('school_' . $field . '_' . date('dmYHis') . Str::random(8)) . '.' . $ext;
        $file->move(public_path('upload/school/'), $fileName);
        return $fileName;
    }

    private function deleteFile(?string $filename): void
    {
        if ($filename) {
            $path = public_path('upload/school/' . $filename);
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
