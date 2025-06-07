<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicateModel extends Model
{
    use HasFactory;

    protected $table = 'communicates';

    protected $fillable = [
        "title",
        "notice_date",
        "publish_date",
        "message",
        "created_by"
    ];

    protected $hidden = [
        'is_delete',
    ];

    static public function getNoticeBoard(int $perpage) {
        return CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('communicates.is_delete', 0)
            ->orderBy('communicates.id', 'desc')
            ->paginate($perpage);
    }

    static public function getNoticeBoardMessage(){
        return CommunicateModel::hasMany(NoticeBoardMessageModel::class, 'communicates_id');
    }


}
