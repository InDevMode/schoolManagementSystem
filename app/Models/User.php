<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'admission_number',
        'address',
        'roll_number',
        'gender',
        'caste',
        'religion',
        'mobile_number',
        'admission_date',
        'date_of_birth',
        'blood_group',
        'height',
        'weight',
        'status',
        'email',
        'password',
        'note',
        'occupation',
        'work_experience',
        'marital_status',
        'permanent_address',
        'profile_picture',
        'class_id',
        'parent_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'user_type',
        'is_delete',
        'remember_token',
        'last_login',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login' => 'datetime',
    ];

    public static function getSingle(int $id)
    {
        return User::find($id);
    }

    public static function getAllAdmin(int $perPage)
    {
        $results = User::select('users.*')
            ->where('user_type', '=', 1);

        $filters = [
            'users.name' => strtolower(Request::get('name')),
            'users.last_name' => strtolower(Request::get('last_name')),
            'users.email' => strtolower(Request::get('email')),
            'users.created_at' => strtolower(Request::get('created_at')),
            'users.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('users.status', $status);
        }

        return $results->where('is_delete', '=', 0)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public static function getEmailSingle(string $email)
    {
        return User::where('email', '=', $email)->first();
    }

    public static function checkEmailSingle(string $email, int $id)
    {
        return User::where('email', $email)->where('id', '!=', $id)->first();
    }

    public static function getTokenSingle(string $token)
    {
        return User::where('remember_token', '=', $token)->first();
    }

    public static function getAllStudent(int $perPage)
    {
        $results = User::select('users.*', 'class.name as class_name', 'parent.name as parent_name', 'parent.last_name as parent_last_name')
            ->join('users as parent', 'parent.id', '=', 'users.parent_id', 'left')
            ->join('class', 'class.id', '=', 'users.class_id')
            ->where('users.user_type', 3)
            ->where('users.is_delete', '=', 0);

        $filters = [
            'users.admission_number' => strtolower(Request::get('admission_number')),
            'users.name' => strtolower(Request::get('name')),
            'users.last_name' => strtolower(Request::get('last_name')),
            'users.email' => strtolower(Request::get('email')),
            'users.mobile_number' => strtolower(Request::get('mobile_number')),
            'users.date_of_birth' => strtolower(Request::get('date_of_birth')),
            'class.name' => strtolower(Request::get('class_name')),
            'users.height' => strtolower(Request::get('height')),
            'users.weight' => strtolower(Request::get('weight')),
            'users.religion' => strtolower(Request::get('religion')),
            'users.created_at' => strtolower(Request::get('created_at')),
            'users.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }
        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('users.status', $status);
        }
        $gender = Request::get('gender');
        if (in_array($gender, ['male', 'female', 'other'], true)) {
            $results->where('users.gender', $gender);
        }
        $blood_group = Request::get('blood_group');
        if (in_array($blood_group, ['a+', 'a-', 'b+', 'b-', 'ab+', 'ab-', 'o+', 'o-'], true)) {
            $results->where('users.blood_group', $blood_group);
        }

        return $results->orderBy('users.id', 'desc')
            ->paginate($perPage);
    }

    public static function getAllParent(int $perPage)
    {
        $results = User::select('users.*')
            ->where('users.user_type', 4)
            ->where('users.is_delete', '=', 0)
            ->where('users.is_delete', '=', 0);

        $filters = [
            'users.name' => strtolower(Request::get('name')),
            'users.last_name' => strtolower(Request::get('last_name')),
            'users.email' => strtolower(Request::get('email')),
            'users.mobile_number' => strtolower(Request::get('mobile_number')),
            'users.occupation' => strtolower(Request::get('occupation')),
            'users.address' => strtolower(Request::get('address')),
            'users.created_at' => strtolower(Request::get('created_at')),
            'users.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }
        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('users.status', $status);
        }
        $gender = Request::get('gender');
        if (in_array($gender, ['male', 'female', 'other'], true)) {
            $results->where('users.gender', $gender);
        }

        return $results->orderBy('users.id', 'desc')
            ->paginate($perPage);
    }

    public static function getAllTeacher(int $perPage)
    {
        $results = User::select('users.*')
            ->where('users.user_type', 2);

        $filters = [
            'users.name' => strtolower(Request::get('name')),
            'users.note' => strtolower(Request::get('note')),
            'users.email' => strtolower(Request::get('email')),
            'users.address' => strtolower(Request::get('address')),
            'users.last_name' => strtolower(Request::get('last_name')),
            'users.occupation' => strtolower(Request::get('occupation')),
            'users.mobile_number' => strtolower(Request::get('mobile_number')),
            'users.permanent_address' => strtolower(Request::get('permanent_address')),
            'users.marital_status' => strtolower(Request::get('marital_status')),
            'users.work_experience' => strtolower(Request::get('work_experience')),
            'users.admission_date' => strtolower(Request::get('admission_date')),
            'users.date_of_birth' => strtolower(Request::get('date_of_birth')),
            'users.created_at' => strtolower(Request::get('created_at')),
            'users.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }
        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('users.status', $status);
        }
        $gender = Request::get('gender');
        if (in_array($gender, ['male', 'female', 'other'], true)) {
            $results->where('users.gender', $gender);
        }

        return $results->where('users.is_delete', '=', 0)
            ->orderBy('users.id', 'desc')
            ->groupBy('users.id')
            ->paginate($perPage);
    }

    public function getProfile(): string
    {
        $path = base_path('upload/profile/' . $this->profile_picture);
        if (!empty($this->profile_picture) && file_exists($path)) {
            return url('upload/profile/' . $this->profile_picture);
        }
        // Image par défaut si rien n'existe
        return url('upload/default.jpg');
    }

    public static function getStudentList(int $perPage)
    {
        $results = User::select('users.*', 'class.name as class_name', 'parent.name as parent_name', 'parent.last_name as parent_last_name')
            ->join('users as parent', 'parent.id', '=', 'users.parent_id', 'left')
            ->join('class', 'class.id', '=', 'users.class_id', 'left')
            ->where('users.user_type', '=', 3)
            ->where('users.is_delete', '=', 0)
            ->where('users.status', '=', 1)
            ->where('class.is_delete', '=', 0)
            ->where('class.status', '=', 1)
            ->whereNull('users.parent_id');

        $filters = [
            'users.name' => strtolower(Request::get('name')),
            'users.last_name' => strtolower(Request::get('last_name')),
            'users.email' => strtolower(Request::get('email')),
            'users.created_at' => strtolower(Request::get('created_at')),
            'users.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        return $results->where('users.is_delete', '=', 0)
            ->orderBy('users.id', 'desc')
            ->paginate($perPage);
    }

    public static function getMyStudent(int $perPage, int $parent_id, )
    {
        $results = User::select(
            'users.*',
            'class.name as class_name',
            'teacher.name as teacher_name',
            'teacher.last_name as teacher_last_name',
            'parent.name as parent_name',
            'parent.last_name as parent_last_name',
            'student.name as student_name',
            'student.last_name as student_last_name'
        )
            ->join('users as parent', 'parent.id', '=', 'users.parent_id', 'left')
            ->join('users as student', 'student.id', '=', 'users.id', 'left')
            ->join('class', 'class.id', '=', 'users.class_id', 'left')
            ->join('class_teacher', 'class_teacher.class_id', '=', 'class.id', 'left')
            ->join('users as teacher', 'teacher.id', '=', 'class_teacher.teacher_id', 'left')
            ->where('users.is_delete', '=', 0)
            ->where('users.status', '=', 1)
            ->where('class.is_delete', '=', 0)
            ->where('class.status', '=', 1)
            ->where('users.parent_id', '=', $parent_id)
            ->where('users.user_type', '=', 3);

        $filters = [
            'users.admission_number' => strtolower(Request::get('admission_number')),
            'users.name' => strtolower(Request::get('student_name')),
            'student.last_name' => strtolower(Request::get('student_last_name')),
            'teacher.name' => strtolower(Request::get('teacher_name')),
            'teacher.last_name' => strtolower(Request::get('teacher_last_name')),
            'parent.name' => strtolower(Request::get('parent_name')),
            'parent.last_name' => strtolower(Request::get('parent_last_name')),
            'class.name' => strtolower(Request::get('class_name')),
            'users.email' => strtolower(Request::get('email')),
            'users.created_at' => strtolower(Request::get('created_at')),
            'users.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $gender = Request::get('gender');
        if (in_array($gender, ['male', 'female', 'other'], true)) {
            $results->where('users.gender', $gender);
        }

        return $results->where('users.is_delete', '=', 0)
            ->orderBy('users.id', 'desc')
            ->groupBy('users.id')
            ->paginate($perPage);
    }

    public static function getTeacher()
    {
        $results = User::select('users.*')
            ->where('users.user_type', '=', 2)
            ->where('users.is_delete', '=', 0);
        return $results->orderBy('users.id', 'desc')
            ->get();
    }

    public static function getTeacherStudent(int $perPage, int $teacher_id)
    {
        $results = User::select('users.*', 'class.name as class_name')
            ->join('class', 'class.id', '=', 'users.class_id')
            ->join('class_teacher', 'class_teacher.class_id', '=', 'class.id')
            ->where('class_teacher.teacher_id', '=', $teacher_id)
            ->where('class_teacher.status', '=', 1)
            ->where('class_teacher.is_delete', '=', 0)
            ->where('users.is_delete', '=', 0)
            ->where('users.status', '=', 1)
            ->where('class.is_delete', '=', 0)
            ->where('class.status', '=', 1)
            ->where('users.user_type', '=', 3)
            ->where('users.is_delete', '=', 0);

        $filters = [
            'users.admission_number' => strtolower(Request::get('admission_number')),
            'users.name' => strtolower(Request::get('name')),
            'users.last_name' => strtolower(Request::get('last_name')),
            'users.email' => strtolower(Request::get('email')),
            'users.date_of_birth' => strtolower(Request::get('date_of_birth')),
            'users.created_at' => strtolower(Request::get('created_at')),
            'users.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('users.status', $status);
        }
        $gender = Request::get('gender');
        if (in_array($gender, ['male', 'female', 'other'], true)) {
            $results->where('users.gender', $gender);
        }

        return $results
            ->orderBy('users.id', 'desc')
            ->paginate($perPage);
    }

    public static function getStudent(int $class_id)
    {
        return User::select('users.id', 'users.name', 'users.last_name')
            ->where('users.is_delete', '=', 0)
            ->where('users.status', '=', 1)
            ->where('users.user_type', '=', 3)
            ->where('users.class_id', '=', $class_id)
            ->orderBy('users.id', 'desc')
            ->get();
    }

    public static function getAttendance(int $student_id, int $class_id, string $date)
    {
        return StudentAttendanceModel::checkAlreadyAttendance($student_id, $class_id, $date);
    }

    public static function getUsers()
    {
        return User::select('id', 'name', 'last_name', 'user_type')
            ->whereIn('user_type', [1, 2, 3, 4])
            ->where('status', 1)
            ->where('is_delete', 0)
            ->get()
            ->map(function ($user) {
                $suffix = match ((int) $user->user_type) {
                    1 => 'Admin',
                    2 => 'Professeur',
                    3 => 'Apprenant',
                    4 => 'Parent',
                    default => '',
                };

                $user->suffix = $suffix;
                $user->full_name = "{$user->name} {$user->last_name} - {$suffix}";
                return $user;
            });
    }

    public static function getUserByUserType(int $user_type)
    {
        return User::select('users.*')
            ->where('user_type', $user_type)
            ->where('is_delete', 0)
            ->where('status', 1)
            ->get();
    }

    public static function getFeesCollectionStudent(int $perpage)
    {
        $results = User::select(
            'users.*',
            'class.name as class_name',
            'class.amount as class_amount',
            'feescollections.paid_amount as paid_amount',
            'feescollections.remaning_amount as remaning_amount',
            'feescollections.created_at as created_at',
            'created_by.name as created_by_name',
            'users.name as student_name',
            'users.last_name as student_last_name',
            'users.mobile_number as student_phone',
            'users.email as student_email',
            'users.admission_number as student_admission_number',
        )
            ->leftJoin('class', 'class.id', '=', 'users.class_id')
            ->leftJoin('feescollections', 'feescollections.student_id', '=', 'users.id')
            ->leftJoin('users as created_by', 'feescollections.created_by', '=', 'created_by.id')
            ->where('users.is_delete', 0)
            ->where('users.status', 1)
            ->where('users.user_type', 3);

        $filters = [
            'class_id' => Request::get('class_id'),
            'student_name' => Request::get('student_name'),
            'student_last_name' => Request::get('student_last_name'),
            'admission_number' => Request::get('admission_number'),
            'created_at' => Request::get('created_at'),
            'updated_at' => Request::get('updated_at'),
        ];

        $map = [
            'class_id' => ['users.class_id', '='],
            'student_name' => ['users.name', 'like'],
            'student_last_name' => ['users.last_name', 'like'],
            'admission_number' => ['users.admission_number', 'like'],
            'created_at' => ['feescollections.created_at', 'date'],
            'updated_at' => ['feescollections.updated_at', 'date'],
        ];

        foreach ($map as $key => [$column, $operator]) {
            $value = $filters[$key] ?? null;

            if ($value === null || $value === '')
                continue;

            match ($operator) {
                'like' => $results->where($column, 'like', '%' . $value . '%'),
                'date' => $results->whereDate($column, $value),
                default => $results->where($column, $value),
            };
        }

        return $results->orderBy('users.name', 'asc')->paginate($perpage);
    }

    public static function getFeesCollectsStudent()
    {
        return User::select(
            'users.*',
            'class.name as class_name',
            'class.amount as class_amount',
            'feescollections.paid_amount as paid_amount',
            'feescollections.remaning_amount as remaning_amount',
            'feescollections.created_at as created_at',
            'created_by.name as created_by_name',
            'users.id as student_id',
            'users.name as student_name',
            'users.last_name as student_last_name',
            'users.admission_number as student_admission_number'
        )
            ->leftJoin('class', 'class.id', '=', 'users.class_id')
            ->leftJoin('feescollections', 'feescollections.student_id', '=', 'users.id')
            ->leftJoin('users as created_by', 'feescollections.created_by', '=', 'created_by.id')
            ->where('users.is_delete', 0)
            ->where('users.status', 1)
            ->where('users.user_type', 3)
            ->first();
    }

    public static function getSingleClass(int $id)
    {
        return User::select('users.*', 'class.name as class_name', 'class.amount as class_amount')
            ->join('class', 'class.id', '=', 'users.class_id')
            ->where('users.id', $id)
            ->first();
    }

    public static function getTotalUserWithUserType(int $user_type)
    {
        return User::select('users.id')
            ->where('user_type', $user_type)
            ->where('is_delete', 0)
            ->count();
    }

    public static function getTotalUser()
    {
        return User::select('users.id')
            ->where('is_delete', 0)
            ->count();
    }

    public static function getTotalTeacherStudent()
    {
        return User::select('users.id')
            ->join('class', 'class.id', '=', 'users.class_id')
            ->join('class_teacher', 'class_teacher.class_id', '=', 'class.id')
            ->where('class_teacher.teacher_id', '=', Auth::user()->id)
            ->where('users.user_type', 3)
            ->where('users.is_delete', 0)
            ->count();
    }

    public static function getTotalParentStudent()
    {
        return User::select('users.id')
            ->join('class', 'class.id', '=', 'users.class_id')
            ->join('users as parent', 'parent.id', '=', 'users.parent_id')
            ->where('users.parent_id', '=', Auth::user()->id)
            ->where('users.user_type', 3)
            ->where('users.is_delete', 0)
            ->count();
    }

    public static function getStudentIds()
    {
        $results = User::select('users.id')
            ->join('class', 'class.id', '=', 'users.class_id')
            ->join('users as parent', 'parent.id', '=', 'users.parent_id')
            ->where('users.parent_id', Auth::user()->id)
            ->where('users.user_type', 3)
            ->where('users.is_delete', 0)
            ->get();

        $student_ids = array();
        foreach ($results as $result) {
            $student_ids[] = $result->id;
        }
        return $student_ids;
    }

    public static function getClassIds()
    {
        $results = User::select('users.*')
            ->join('class', 'class.id', '=', 'users.class_id')
            ->join('users as parent', 'parent.id', '=', 'users.parent_id')
            ->where('users.parent_id', Auth::user()->id)
            ->where('users.user_type', 3)
            ->where('users.is_delete', 0)
            ->get();

        $class_ids = array();
        foreach ($results as $result) {
            $class_ids[] = $result->class_id;
        }
        return $class_ids;
    }


}
