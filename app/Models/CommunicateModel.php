<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class CommunicateModel extends Model
{
    use HasFactory;

    protected $table = 'communicates';

    protected $fillable = [
        "school_id",
        "title",
        "notice_date",
        "publish_date",
        "message",
        "created_by",
        "is_active",
        "email_sent_at",
        "deleted_at",
        "deleted_reason",
    ];

    protected $hidden = [];

    protected $casts = [
        'is_active'      => 'boolean',
        'deleted_at'     => 'datetime',
        'email_sent_at'  => 'datetime',
    ];

    public static function getSingle($id)
    {
        return CommunicateModel::find($id);
    }

    public static function getNoticeBoard(int $perpage)
    {
        $user         = \Illuminate\Support\Facades\Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('communicates.is_delete', '=', 0);

        if (! $isSuperAdmin && $user) {
            $q->where('communicates.school_id', $user->school_id);
        }

        return $q->orderBy('communicates.id', 'desc')->paginate($perpage);
    }

    /**
     * Retourne une notice avec ses destinataires (message_to)
     */
    public static function getSingleWithRecipients(int $id): ?self
    {
        $notice = CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('communicates.id', $id)
            ->first();

        if ($notice) {
            $notice->recipients = NoticeBoardMessageModel::where('communicates_id', $id)
                ->pluck('message_to')
                ->map(fn($v) => (string) $v)
                ->toArray();
        }

        return $notice;
    }

    /**
     * Liste du tableau d'affichage avec les destinataires
     */
    public static function getNoticeBoardWithRecipients(int $perpage)
    {
        $user         = \Illuminate\Support\Facades\Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('communicates.is_delete', '=', 0);

        // Scoping multi-tenant : un admin ne voit que les notices de son école
        if (! $isSuperAdmin && $user) {
            $q->where('communicates.school_id', $user->school_id);
        }

        $notices = $q->orderBy('communicates.id', 'desc')->paginate($perpage);

        // Charger les destinataires pour chaque notice
        $noticeIds = $notices->pluck('id')->toArray();
        $recipients = NoticeBoardMessageModel::whereIn('communicates_id', $noticeIds)
            ->get()
            ->groupBy('communicates_id');

        $notices->getCollection()->transform(function ($notice) use ($recipients) {
            $notice->recipients = isset($recipients[$notice->id])
                ? $recipients[$notice->id]->pluck('message_to')->map(fn($v) => (string) $v)->toArray()
                : [];
            return $notice;
        });

        return $notices;
    }

    /**
     * Historique des notifications supprimées — scoping école.
     */
    public static function getDeletedNoticeBoard(int $perpage)
    {
        $user         = \Illuminate\Support\Facades\Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('communicates.is_delete', '=', 1);

        if (! $isSuperAdmin && $user) {
            $q->where('communicates.school_id', $user->school_id);
        }

        return $q->orderBy('communicates.id', 'desc')->paginate($perpage);
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

    public static function getNoticeBoardWithUserType(int $message_to, int $perpage)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $results = CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('noticeboard_messages', 'noticeboard_messages.communicates_id', '=', 'communicates.id')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('noticeboard_messages.message_to', '=', $message_to)
            ->where('communicates.is_delete', '=', 0)
            ->where('communicates.is_active', '=', 1);

        // Scoping multi-tenant : l'élève/parent/prof ne voit que les notices de son école
        if ($user && $user->school_id) {
            $results->where('communicates.school_id', $user->school_id);
        }

        $filters = [
            'communicates.title' => strtolower(Request::get('title')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        if (!empty(Request::get('date_notice_from'))) {
            $results->whereDate('notice_date', '>=', Request::get('date_notice_from'));
        }

        if (!empty(Request::get('date_notice_to'))) {
            $results->whereDate('notice_date', '<=', Request::get('date_notice_to'));
        }

        if (!empty(Request::get('date_publish_from'))) {
            $results->whereDate('publish_date', '>=', Request::get('date_publish_from'));
        }

        if (!empty(Request::get('date_publish_to'))) {
            $results->whereDate('publish_date', '<=', Request::get('date_publish_to'));
        }

        $paginated = $results->orderBy('communicates.id', 'desc')
            ->paginate($perpage);

        // Charger les destinataires pour chaque notice
        $noticeIds  = $paginated->pluck('id')->toArray();
        $recipients = NoticeBoardMessageModel::whereIn('communicates_id', $noticeIds)
            ->get()
            ->groupBy('communicates_id');

        $paginated->getCollection()->transform(function ($notice) use ($recipients) {
            $notice->recipients = isset($recipients[$notice->id])
                ? $recipients[$notice->id]->pluck('message_to')->map(fn($v) => (string) $v)->toArray()
                : [];
            return $notice;
        });

        return $paginated;
    }

    public static function getTotalCommunicate()
    {
        return CommunicateModel::where('is_delete', 0)->count();
    }

    public static function getTotalCommunicateCreatedByTeacher()
    {
        return CommunicateModel::where('is_delete', 0)->where('created_by', Auth::user()->id)->count();
    }

    public static function getCommunicateWithUserType(int $user_type)
    {
        return CommunicateModel::select('communicates.*', 'users.name as created_by_name')
            ->join('noticeboard_messages', 'noticeboard_messages.communicates_id', '=', 'communicates.id')
            ->join('users', 'users.id', '=', 'communicates.created_by')
            ->where('noticeboard_messages.message_to', '=', $user_type)
            ->where('communicates.is_delete', '=', 0)
            ->orderBy('communicates.id', 'desc')
            ->get();
    }


}
