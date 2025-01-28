<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Request;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'user_type',
        'class_id',
        'parent_id',
        'is_delete',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    static public function getSingle(int $id)
    {
        return User::find($id);
    }

    static public function getAllAdmin(int $perPage)
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

    static public function getEmailSingle(string $email)
    {
        return User::where('email', '=', $email)->first();
    }

    static public function checkEmailSingle(string $email, int $id)
    {
        return User::where('email', $email)->where('id', '!=', $id)->first();
    }

    static public function getTokenSingle(string $token)
    {
        return User::where('remember_token', '=', $token)->first();
    }

    static public function getAllStudent(int $perPage)
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

    static public function getAllParent(int $perPage)
    {
        $results = User::select('users.*')
            ->where('users.user_type', 4)
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

    static public function getAllTeacher(int $perPage)
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
            ->paginate($perPage);
    }

    static public function getProfile(string $profilePicture): string
    {
        if (!empty($profilePicture) && file_exists('upload/profile/' . $profilePicture)) {
            return url('upload/profile/' . $profilePicture);
        }
        return url('');
    }

    static public function getStudentList(int $perPage)
    {
        $results = User::select('users.*', 'class.name as class_name', 'parent.name as parent_name', 'parent.last_name as parent_last_name')
            ->join('users as parent', 'parent.id', '=', 'users.parent_id', 'left')
            ->join('class', 'class.id', '=', 'users.class_id', 'left')
            ->where('users.user_type', '=', 3)
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

    static public function getMyStudent(int $parent_id, int $perPage)
    {
        $results = User::select('users.*', 'class.name as class_name', 'parent.name as parent_name', 'parent.last_name as parent_last_name')
            ->join('users as parent', 'parent.id', '=', 'users.parent_id', 'left')
            ->join('class', 'class.id', '=', 'users.class_id', 'left')
            ->where('users.parent_id', '=', $parent_id)
            ->where('users.user_type', '=', 3);

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

}
