<?php

namespace App\Http\Controllers;

use App\Exports\ExportStudent;
use App\Models\ClassModel;
use App\Models\ClassTeacherModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\UploadService;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function list()
    {
        $perPage  = min((int) request('per_page', 15), 100);
        $students = User::getAllStudent($perPage);

        // Enrichir avec school_name (évite le N+1)
        $schoolIds = $students->getCollection()->pluck('school_id')->filter()->unique()->values();
        $schools   = \App\Models\School::whereIn('id', $schoolIds)->get()->keyBy('id');

        $students->getCollection()->transform(function ($s) use ($schools) {
            $s->is_online   = \Illuminate\Support\Facades\Cache::has('OnlineUser.' . $s->id);
            $school         = $s->school_id ? ($schools[$s->school_id] ?? null) : null;
            $s->school_name = $school?->school_name ?? null;
            return $s;
        });

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'classes'  => ClassModel::getClass(),
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email|unique:users,email',
            'class_id'         => 'required|exists:class,id',
            'status'           => 'required|in:0,1',
            'gender'           => 'nullable|in:male,female,other',
            'admission_number' => 'nullable|string|max:50',
            'password'         => 'nullable|string|min:6',
        ]);

        try {
            $student = new User;

            if ($request->hasFile('profile_picture')) {
                $student->profile_picture = $this->uploadProfilePicture($request, 'student');
            }

            if ($request->filled('date_of_birth')) {
                $dob = Carbon::parse($request->date_of_birth);
                if ($dob->diffInYears(now()) < 2) {
                    return back()->with('error', "L'apprenant doit avoir au moins 2 ans.");
                }
                $student->date_of_birth = $dob;
            }

            $student->fill([
                'name'             => trim($request->name),
                'last_name'        => trim($request->last_name),
                'email'            => trim($request->email),
                'admission_number' => $request->admission_number,
                'roll_number'      => $request->roll_number,
                'class_id'         => $request->class_id,
                'gender'           => $request->gender,
                'caste'            => $request->caste,
                'religion'         => $request->religion,
                'mobile_number'    => $request->mobile_number,
                'admission_date'   => $request->admission_date,
                'blood_group'      => $request->blood_group,
                'height'           => $request->height,
                'weight'           => $request->weight,
                'status'           => $request->status,
                'user_type'        => 3,
                'created_by'       => Auth::id(),
                'school_id'        => (Auth::user()->user_type !== 0) ? Auth::user()->school_id : null,
            ]);

            if ($request->filled('password')) {
                $student->password = Hash::make($request->password);
            }

            $student->save();

            return back()->with('success', 'Apprenant créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Création apprenant : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function update(Request $request, int $id)
    {
        $student = User::getSingle($id);
        abort_unless($student, 404);

        $request->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email'     => "required|email|unique:users,email,{$id}",
            'class_id'  => 'required|exists:class,id',
            'status'    => 'required|in:0,1',
            'password'  => 'nullable|string|min:6',
        ]);

        try {
            if ($request->filled('date_of_birth')) {
                $dob = Carbon::parse($request->date_of_birth);
                if ($dob->diffInYears(now()) < 2) {
                    return back()->with('error', "L'apprenant doit avoir au moins 2 ans.");
                }
                $student->date_of_birth = $dob;
            }

            $student->fill([
                'name'             => trim($request->name),
                'last_name'        => trim($request->last_name),
                'email'            => trim($request->email),
                'admission_number' => $request->admission_number,
                'roll_number'      => $request->roll_number,
                'class_id'         => $request->class_id,
                'gender'           => $request->gender,
                'caste'            => $request->caste,
                'religion'         => $request->religion,
                'mobile_number'    => $request->mobile_number,
                'admission_date'   => $request->admission_date,
                'blood_group'      => $request->blood_group,
                'height'           => $request->height,
                'weight'           => $request->weight,
                'status'           => $request->status,
            ]);

            if ($request->filled('password')) {
                $student->password = Hash::make($request->password);
            }

            if ($request->hasFile('profile_picture')) {
                $this->deleteOldPicture($student->profile_picture);
                $student->profile_picture = $this->uploadProfilePicture($request, 'student');
            }

            $student->save();

            return back()->with('success', 'Apprenant modifié avec succès.');
        } catch (\Exception $e) {
            Log::error('Modification apprenant : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function delete(int $id)
    {
        $student = User::getSingle($id);
        abort_unless($student, 404);
        $student->update(['is_delete' => 1]);
        return back()->with('success', 'Apprenant supprimé avec succès.');
    }

    public function myStudent()
    {
        $teacherId = Auth::id();

        // Compte d'apprenants par classe pour ce professeur
        $studentsByClass = \App\Models\ClassTeacherModel::select(
                'class.id as class_id',
                'class.name as class_name',
                \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT users.id) as student_count')
            )
            ->join('class', 'class.id', '=', 'class_teacher.class_id')
            ->join('users', function ($join) {
                $join->on('users.class_id', '=', 'class.id')
                     ->where('users.user_type', 3)
                     ->where('users.is_delete', 0)
                     ->where('users.status', 1);
            })
            ->where('class_teacher.teacher_id', $teacherId)
            ->where('class_teacher.status', 1)
            ->where('class_teacher.is_delete', 0)
            ->where('class.is_delete', 0)
            ->where('class.status', 1)
            ->groupBy('class.id', 'class.name')
            ->get();

        return Inertia::render('Teacher/Students/Index', [
            'students'       => User::getTeacherStudent(15, $teacherId),
            'studentsByClass' => $studentsByClass,
        ]);
    }

    public function exportStudent()
    {
        return Excel::download(new ExportStudent, 'students_' . date('d_m_Y') . '.xlsx');
    }

    private function uploadProfilePicture(Request $request, string $prefix): string
    {
        return UploadService::upload($request->file('profile_picture'), UploadService::profileFolder(), $prefix);
    }

    private function deleteOldPicture(?string $path): void
    {
        UploadService::delete($path);
    }
}
