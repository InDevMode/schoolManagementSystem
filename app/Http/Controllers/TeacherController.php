<?php

namespace App\Http\Controllers;

use App\Exports\ExportTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/Teachers/Index', [
            'teachers' => User::getAllTeacher(15),
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'nullable|string|min:6',
            'status'    => 'required|in:0,1',
            'gender'    => 'nullable|in:male,female,other',
        ]);

        try {
            $teacher = new User;

            if ($request->hasFile('profile_picture')) {
                $teacher->profile_picture = $this->uploadProfilePicture($request, 'teacher');
            }

            $teacher->fill([
                'name'              => trim($request->name),
                'last_name'         => trim($request->last_name),
                'email'             => trim($request->email),
                'gender'            => $request->gender,
                'mobile_number'     => $request->mobile_number,
                'date_of_birth'     => $request->date_of_birth,
                'admission_date'    => $request->admission_date,
                'marital_status'    => $request->marital_status,
                'address'           => $request->address,
                'permanent_address' => $request->permanent_address,
                'work_experience'   => $request->work_experience,
                'note'              => $request->note,
                'status'            => $request->status,
                'user_type'         => 2,
                'created_by'        => Auth::id(),
            ]);

            if ($request->filled('password')) {
                $teacher->password = Hash::make($request->password);
            }

            $teacher->save();

            return back()->with('success', 'Professeur créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Création professeur : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function update(Request $request, int $id)
    {
        $teacher = User::getSingle($id);
        abort_unless($teacher, 404);

        $request->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email'     => "required|email|unique:users,email,{$id}",
            'password'  => 'nullable|string|min:6',
            'status'    => 'required|in:0,1',
        ]);

        try {
            $teacher->fill([
                'name'              => trim($request->name),
                'last_name'         => trim($request->last_name),
                'email'             => trim($request->email),
                'gender'            => $request->gender,
                'mobile_number'     => $request->mobile_number,
                'date_of_birth'     => $request->date_of_birth,
                'admission_date'    => $request->admission_date,
                'marital_status'    => $request->marital_status,
                'address'           => $request->address,
                'permanent_address' => $request->permanent_address,
                'work_experience'   => $request->work_experience,
                'note'              => $request->note,
                'status'            => $request->status,
            ]);

            if ($request->filled('password')) {
                $teacher->password = Hash::make($request->password);
            }

            if ($request->hasFile('profile_picture')) {
                $this->deleteOldPicture($teacher->profile_picture);
                $teacher->profile_picture = $this->uploadProfilePicture($request, 'teacher');
            }

            $teacher->save();

            return back()->with('success', 'Professeur modifié avec succès.');
        } catch (\Exception $e) {
            Log::error('Modification professeur : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function delete(int $id)
    {
        $teacher = User::getSingle($id);
        abort_unless($teacher, 404);
        $teacher->update(['is_delete' => 1]);
        return back()->with('success', 'Professeur supprimé avec succès.');
    }

    public function exportTeacher()
    {
        return Excel::download(new ExportTeacher, 'teachers_' . date('d_m_Y') . '.xlsx');
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
