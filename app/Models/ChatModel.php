<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatModel extends Model
{
    use HasFactory;

    protected $table = 'chats';
    protected $fillable = ['receiver_id', 'sender_id', 'message', 'status', 'file', 'is_delete'];
    protected $hidden = ['is_delete'];

    public function getSingle(int $id)
    {
        return ChatModel::find($id);
    }

}
