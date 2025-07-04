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

    public static function getSingle($id)
    {
        return CommunicateModel::find($id);
    }

    public static function getNoticeBoard(int $perpage)
    {
        return CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('communicates.is_delete', 0)
            ->orderBy('communicates.id', 'desc')
            ->paginate($perpage);
    }

    // Méthode utilisé dans le frontend pour récupérer les utilisateurs à qui on a envoyé un message
    public function getNoticeBoardMessage()
    {
        return CommunicateModel::hasMany(NoticeBoardMessageModel::class, 'communicates_id');
    }

    // Méthode utilisé dans le frontend pour cocher par défaut les utilisateurs à qui on a envoyé un message
    public function getMessageToSingle($communicates_id, $message_to)
    {
        return NoticeBoardMessageModel::where('communicates_id', $communicates_id)->where('message_to', $message_to)->first();
    }


}
