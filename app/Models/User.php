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
        'email',
        'password',
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

    static public function getSingle($id)
    {
        return User::find($id);
    }

    static public function getAllAdmin()
    {
        $results = User::select('users.*')
            ->where('user_type', '=', 1)
            ->where('is_delete', '=', 0);

        $filters = [
            'name' => 'like',
            'email' => 'like',
            'created_at' => 'like',
            'updated_at' => 'like',
        ];

        foreach ($filters as $field => $operator) {
            if (!empty(Request::get($field))) {
                $value = $operator === 'like' ? '%' . Request::get($field) . '%' : Request::get($field);
                $results = $results->where($field, $operator, $value);
            }
        }

        return $results->orderBy('id', 'desc')->paginate(5);
    }

    static public function getEmailSingle($email)
    {
        return User::where('email', '=', $email)->first();
    }

    static public function checkEmailSingle($email, $id)
    {
        return User::where('email', $email)->where('id', '!=', $id)->first();
    }

    static public function getTokenSingle($token)
    {
        return User::where('remember_token', '=', $token)->first();
    }
}
