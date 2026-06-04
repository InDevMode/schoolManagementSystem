<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;

class ChatModel extends Model
{
    use HasFactory;

    protected $table = 'chats';
    protected $fillable = ['receiver_id', 'sender_id', 'message', 'status', 'file',];
    protected $hidden = [];  // is_delete doit être visible dans les réponses JSON
    protected $casts = [
        'created_date' => 'datetime',
    ];


    public static function getSingle(int $id)
    {
        return ChatModel::find($id);
    }

    public static function getChats(int $receiver_id, int $sender_id)
    {
        return ChatModel::select('chats.*')
            ->where(function ($query) use ($receiver_id, $sender_id) {
                $query->where(function ($query) use ($receiver_id, $sender_id) {
                    $query->where('receiver_id', $receiver_id)
                        ->where('sender_id', $sender_id);
                })
                    ->orWhere(function ($query) use ($receiver_id, $sender_id) {
                        $query->where('receiver_id', $sender_id)
                            ->where('sender_id', $receiver_id)
                            ->where('status', '>', -1);
                    });
            })
            // Inclure les messages avec fichier même si message vide
            ->where(function ($q) {
                $q->where('message', '!=', '')->orWhereNotNull('file');
            })
            // NE PAS exclure les messages supprimés — on les affiche "Message supprimé"
            // mais on les inclut pour maintenir la cohérence de la conversation
            ->orderBy('id', 'asc')
            ->get();
    }

    public function getSender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public static function getMarkAsRead(int $receiverId)
    {
        return ChatModel::where('receiver_id', Auth::id())
            ->where('sender_id', $receiverId)
            ->where('status', '=', 0)
            ->update(['status' => 1]);
    }

    public static function getChatUser(int $user_id)
    {
        $getChatUser = ChatModel::select(
            'chats.*',
            'sender.name as sender_name',
            'sender.last_name as last_name',
            'receiver.name as receiver_name',
            'receiver.last_name as receiver_last_name',
            'receiver.profile_picture as receiver_profile_picture',
            'sender.profile_picture as sender_profile_picture',
            'chats.is_delete',
            \Illuminate\Support\Facades\DB::raw('(CASE WHEN chats.sender_id = ' . $user_id . ' THEN chats.receiver_id ELSE chats.sender_id END) as connection_user_id')
        )
            ->join('users as sender', 'sender.id', '=', 'chats.sender_id')
            ->join('users as receiver', 'receiver.id', '=', 'chats.receiver_id')
            ->whereIn('chats.id', function ($query) use ($user_id) {
                $query->selectRaw('MAX(chats.id)')
                    ->from('chats')
                    ->where(function ($sub) use ($user_id) {
                        $sub->where('chats.receiver_id', $user_id)
                            ->orWhere('chats.sender_id', $user_id);
                    })
                    ->where(function ($sub) {
                        $sub->where('chats.is_delete', 0)
                            ->orWhereNull('chats.is_delete');
                    })
                    ->groupBy(\Illuminate\Support\Facades\DB::raw('CASE WHEN chats.sender_id = ' . $user_id . ' THEN chats.receiver_id ELSE chats.sender_id END'));
            })
            ->where(function ($q) {
                $q->where('chats.is_delete', 0)->orWhereNull('chats.is_delete');
            })
            ->orderBy('chats.id', 'desc')
            ->get();

        $result = [];
        foreach ($getChatUser as $value) {
            $data['id'] = $value->id;
            // Aperçu : si message vide mais fichier, afficher le nom du fichier
            if (empty($value->message) && !empty($value->file)) {
                $ext = pathinfo($value->file, PATHINFO_EXTENSION);
                $data['message'] = '📎 fichier.' . $ext;
            } else {
                $data['message'] = $value->message;
            }
            $data['file'] = $value->file;
            $data['created_date'] = $value->created_date;
            $data['status'] = $value->status;
            $data['is_delete'] = $value->is_delete;
            $data['user_id'] = $value->connection_user_id;
            $data['name'] = $value->getConnectUser->last_name . ' ' . $value->getConnectUser->name;
            $data['last_login'] = $value->getConnectUser->last_login;
            $data['sender_profile_picture'] = $value->getConnectUser->getProfile();
            $data['countMessage'] = $value->countMessage($value->connection_user_id, $user_id);
            $result[] = $data;
        }
        return $result;
    }

    public function countMessage(int $connection_user_id, int $user_id)
    {
        return ChatModel::where('sender_id', '=', $connection_user_id)
            ->where('receiver_id', '=', $user_id)
            ->where('status', 0)
            ->count();
    }

    public function getConnectUser()
    {
        return $this->belongsTo(User::class, 'connection_user_id');
    }

    public static function updateCountMessage(int $sender_id, int $receiver_id)
    {
        ChatModel::where('sender_id', $receiver_id)
            ->where('receiver_id', '=', $sender_id)
            ->update(['status' => 1]);
    }

    public static function getUnreadMessages(int $user_id)
    {
        $getUnread = ChatModel::select(
            'chats.*',
            'sender.name as sender_name',
            'sender.last_name as sender_last_name',
            'sender.last_login as last_login',
            'sender.profile_picture as sender_profile_picture'
        )
            ->join('users as sender', 'sender.id', '=', 'chats.sender_id')
            ->join('users as receiver', 'receiver.id', '=', 'chats.receiver_id')
            ->where('chats.receiver_id', $user_id)
            ->where('chats.status', '!=', 1)
            ->where('chats.is_delete', '=', 0)
            ->orderBy('chats.id', 'desc')
            ->distinct('sender.id')
            ->get();

        $result = [];

        foreach ($getUnread as $value) {
            $data['id'] = $value->id;
            $data['message'] = $value->message;
            $data['created_date'] = $value->created_date;
            $data['sender_id'] = $value->sender_id;
            $data['sender_name'] = $value->sender_last_name . ' ' . $value->sender_name;
            $data['sender_profile_picture'] = $value->sender_profile_picture;
            $data['sender_last_login'] = $value->last_login;
            $data['countMessage'] = $value->countMessage($value->id, $user_id);
            $result[] = $data;
        }

        return $result;
    }

    public static function getAllChatUser()
    {
        return ChatModel::select('chats.id')
            ->join('users as sender', 'sender.id', '=', 'chats.sender_id')
            ->join('users as receiver', 'receiver.id', '=', 'chats.receiver_id')
            ->where('chats.receiver_id', '=', Auth::user()->id)
            ->where('chats.status', '=', 0)
            ->where('chats.is_delete', '=', 0)
            ->count();
    }

    public function getChatFile(): string
    {
        $path = base_path('upload/chats/' . $this->file);
        if (!empty($this->file) && file_exists($path)) {
            return url('upload/chats/' . $this->file);
        }
        // Image par défaut si rien n'existe
        return url('');
    }


}
