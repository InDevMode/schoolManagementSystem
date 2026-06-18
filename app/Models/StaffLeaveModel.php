<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class StaffLeaveModel extends Model
{
    use HasFactory;

    protected $table = 'staff_leaves';

    protected $fillable = [
        'staff_id', 'leave_type_id', 'start_date', 'end_date',
        'reason', 'status', 'approved_by', 'approved_at', 'admin_note',
    ];

    protected $hidden = ['is_delete'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public static function getSingle(int $id): ?self
    {
        return self::find($id);
    }

    public static function getAll(int $perPage)
    {
        $q = self::select(
            'staff_leaves.*',
            'users.name as first_name',
            'users.last_name',
            'staff.role as staff_role',
            'leave_types.name as leave_type_name',
            'leave_types.color as leave_type_color',
            'approver.name as approver_name',
        )
            ->join('staff', 'staff.id', '=', 'staff_leaves.staff_id')
            ->join('users', 'users.id', '=', 'staff.user_id')
            ->join('leave_types', 'leave_types.id', '=', 'staff_leaves.leave_type_id')
            ->leftJoin('users as approver', 'approver.id', '=', 'staff_leaves.approved_by')
            ->where('staff_leaves.is_delete', 0);

        if ($v = Request::get('status'))   $q->where('staff_leaves.status', $v);
        if ($v = Request::get('staff_id')) $q->where('staff_leaves.staff_id', $v);

        return $q->orderBy('staff_leaves.created_at', 'desc')->paginate($perPage);
    }

    public static function getPendingCount(): int
    {
        return self::where('status', 'pending')->where('is_delete', 0)->count();
    }

    public function getDurationDays(): ?int
    {
        if (!$this->end_date) return null;
        return Carbon::parse($this->start_date)->diffInDays($this->end_date) + 1;
    }
}
