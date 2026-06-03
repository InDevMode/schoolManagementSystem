<?php

namespace App\Http\Controllers;

use App\Exports\ExportParent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ParentController extends Controller
{
    public function list()
    {
        $perPage = min((int) request('per_page', 15), 100);
        $parents = User::getAllParent($perPage);
        $parents->getCollection()->transform(function ($p) {
            $p->is_online = \Illuminate\Support\Facades\Cache::has('OnlineUser.' . $p->id);
            return $p;
        });

        return Inertia::render('Admin/Parents/Index', [
            'parents' => $parents,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'status'    => 'required|in:0,1',
            'gender'    => 'nullable|in:male,female,other',
            'password'  => 'nullable|string|min:6',
        ]);

        try {
            $parent = new User;

            if ($request->hasFile('profile_picture')) {
                $parent->profile_picture = $this->uploadProfilePicture($request, 'parent');
            }

            $parent->fill([
                'name'          => trim($request->name),
                'last_name'     => trim($request->last_name),
                'email'         => trim($request->email),
                'occupation'    => $request->occupation,
                'address'       => $request->address,
                'gender'        => $request->gender,
                'mobile_number' => $request->mobile_number,
                'status'        => $request->status,
                'user_type'     => 4,
                'created_by'    => Auth::id(),
            ]);

            if ($request->filled('password')) {
                $parent->password = Hash::make($request->password);
            }

            $parent->save();

            return back()->with('success', 'Parent créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Création parent : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function update(Request $request, int $id)
    {
        $parent = User::getSingle($id);
        abort_unless($parent, 404);

        $request->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email'     => "required|email|unique:users,email,{$id}",
            'status'    => 'required|in:0,1',
            'password'  => 'nullable|string|min:6',
        ]);

        try {
            $parent->fill([
                'name'          => trim($request->name),
                'last_name'     => trim($request->last_name),
                'email'         => trim($request->email),
                'occupation'    => $request->occupation,
                'address'       => $request->address,
                'gender'        => $request->gender,
                'mobile_number' => $request->mobile_number,
                'status'        => $request->status,
            ]);

            if ($request->filled('password')) {
                $parent->password = Hash::make($request->password);
            }

            if ($request->hasFile('profile_picture')) {
                $this->deleteOldPicture($parent->profile_picture);
                $parent->profile_picture = $this->uploadProfilePicture($request, 'parent');
            }

            $parent->save();

            return back()->with('success', 'Parent modifié avec succès.');
        } catch (\Exception $e) {
            Log::error('Modification parent : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function student(int $id)
    {
        return Inertia::render('Admin/Parents/Students', [
            'parent'      => User::getSingle($id),
            'studentList' => User::getStudentList(50),
            'myStudents'  => User::getMyStudent(50, $id),
            'parentId'    => $id,
        ]);
    }

    public function assignStudentParent(int $parent_id, int $student_id)
    {
        User::getSingle($student_id)?->update(['parent_id' => $parent_id]);
        return back()->with('success', 'Apprenant assigné au parent avec succès.');
    }

    public function desAssignStudentParent(int $student_id)
    {
        User::getSingle($student_id)?->update(['parent_id' => null]);
        return back()->with('success', 'Apprenant désassigné avec succès.');
    }

    public function delete(int $id)
    {
        $parent = User::getSingle($id);
        abort_unless($parent, 404);
        $parent->update(['is_delete' => 1]);
        return back()->with('success', 'Parent supprimé avec succès.');
    }

    public function parentStudent()
    {
        $id = Auth::id();
        return Inertia::render('Parent/Students/Index', [
            'myStudents' => User::getMyStudent(15, $id),
        ]);
    }

    public function exportParent()
    {
        return Excel::download(new ExportParent, 'parents_' . date('d_m_Y') . '.xlsx');
    }

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
