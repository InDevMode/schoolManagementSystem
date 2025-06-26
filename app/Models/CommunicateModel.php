<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

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

    public static function getSingle(int $id)
    {
        return CommunicateModel::find($id);
    }

    public static function getNoticeBoard(int $perpage)
    {
        $results = CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('communicates.is_delete', 0);

        $filters = [
            'communicates.title' => strtolower(Request::get('title')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        if (!empty(Request::get('date_notice_to')) && !empty(Request::get('date_notice_from'))) {
            $results->whereBetween('communicates.notice_date', [Request::get('date_notice_to'), Request::get('date_notice_from')]);
        }

        if (!empty(Request::get('publish_date_to')) && !empty(Request::get('publish_date_from'))) {
            $results->whereBetween('communicates.publish_date', [Request::get('publish_date_to'), Request::get('publish_date_from')]);
        }

        if (!empty(Request::get('publish_date_to')) && !empty(Request::get('publish_date_from'))) {
            $results->whereBetween('communicates.publish_date', [Request::get('publish_date_to'), Request::get('publish_date_from')]);
        }

        if ($messageTos = Request::get('message_to')) {
            $results->join('noticeboard_messages', 'noticeboard_messages.communicates_id', '=', 'communicates.id')
                ->whereIn('noticeboard_messages.message_to', array_map('intval', $messageTos));
        }

        return $results->orderBy('communicates.id', 'desc')
            ->paginate($perpage);
    }

    public function getNoticeBoardMessage()
    {
        return CommunicateModel::hasMany(NoticeBoardMessageModel::class, 'communicates_id');
    }

    public function getMessageToSingle(int $noticeBoardId, int $receiverId)
    {
        return NoticeBoardMessageModel::where('communicates_id', $noticeBoardId)->where('message_to', $receiverId)->first();
    }

    public static function getNoticeBoardWithUserType(int $message_to, int $perpage) {
        $results = CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->join('noticeboard_messages', 'noticeboard_messages.communicates_id', '=', 'communicates.id')
            ->where('noticeboard_messages.message_to', '=', $message_to);   // TODO
            // ->where('communicates.is_delete', 0)->whereDate('communicates.publish_date', '<=', Carbon::today()->toDateString());

            $filters = [
                'communicates.title' => strtolower(Request::get('title')),
            ];

            foreach ($filters as $column => $value) {
                if (!empty($value)) {
                    $results->where($column, 'like', '%' . $value . '%');
                }
            }

            if (!empty(Request::get('date_notice_from')) && !empty(Request::get('date_notice_to'))) {
                $results->whereBetween('communicates.notice_date', [Request::get('date_notice_from'), Request::get('date_notice_to')]);
            }

           $results =  $results->orderBy('communicates.id', 'desc')
            ->paginate($perpage);

        return $results;
    }

}
