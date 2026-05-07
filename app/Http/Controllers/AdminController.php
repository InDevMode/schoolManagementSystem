<?php

namespace App\Http\Controllers;

use App\Exports\ExportAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/Admins/Index', [
            'admins' => User::getAllAdmin(15),
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'status'    => 'required|in:0,1',
        ]);

        try {
            $admin = new User;

            if ($request->hasFile('profile_picture')) {
                $admin->profile_picture = $this->uploadProfilePicture($request, 'admin');
            }

            $admin->fill([
                'name'       => trim($request->name),
                'last_name'  => trim($request->last_name),
                'email'      => trim($request->email),
                'status'     => $request->status,
                'password'   => Hash::make($request->password),
                'user_type'  => 1,
                'created_by' => Auth::id(),
            ])->save();

            return back()->with('success', 'Administrateur créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Création admin : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function update(Request $request, int $id)
    {
        $admin = User::getSingle($id);
        abort_unless($admin, 404);

        $request->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email'     => "required|email|unique:users,email,{$id}",
            'password'  => 'nullable|string|min:6',
            'status'    => 'required|in:0,1',
        ]);

        try {
            $admin->fill([
                'name'      => trim($request->name),
                'last_name' => trim($request->last_name),
                'email'     => trim($request->email),
                'status'    => $request->status,
            ]);

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            if ($request->hasFile('profile_picture')) {
                $this->deleteOldPicture($admin->profile_picture);
                $admin->profile_picture = $this->uploadProfilePicture($request, 'admin');
            }

            $admin->save();

            return back()->with('success', 'Administrateur modifié avec succès.');
        } catch (\Exception $e) {
            Log::error('Modification admin : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function delete(int $id)
    {
        $admin = User::getSingle($id);
        abort_unless($admin, 404);

        $admin->update(['is_delete' => 1]);

        return back()->with('success', 'Administrateur supprimé avec succès.');
    }

    public function exportAdmin()
    {
        return Excel::download(new ExportAdmin, 'admins_' . date('d_m_Y') . '.xlsx');
    }

    // ─── Helpers privés ───────────────────────────────────────────────────────

    private function uploadProfilePicture(Request $request, string $prefix): string
    {
        $file = $request->file('profile_picture');
        $fileName = strtolower($prefix . date('dmYhis') . Str::random(10)) . '.' . $file->getClientOriginalExtension();
        $file->move('upload/profile/', $fileName);
        return $fileName;
    }

    private function deleteOldPicture(?string $filename): void
    {
        if ($filename && file_exists('upload/profile/' . $filename)) {
            unlink('upload/profile/' . $filename);
        }
    }
}
