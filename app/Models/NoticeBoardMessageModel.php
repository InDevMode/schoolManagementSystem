<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticeBoardMessageModel extends Model
{
    use HasFactory;

    protected $table = 'noticeboard_messages';

    protected $fillable = [
        "communicates_id",
        "message_to",
        "created_by"
    ];

    protected $hidden = [
        '',
    ];

    public static function deleteNoticeBoardMessage(int $id){
        return NoticeBoardMessageModel::where('communicates_id', $id)->delete();
    }

    public static function getTotalNoticeBoardMessage(){
        return NoticeBoardMessageModel::count();
    }

    public static function getTotalNoticeBoardMessageTeacher(){
        return NoticeBoardMessageModel::where('message_to', 2)->count();
    }

    public static function getTotalNoticeBoardMessageStudent(){
        return NoticeBoardMessageModel::where('message_to', 3)->count();
    }

    public static function getTotalNoticeBoardMessageParent(){
        return NoticeBoardMessageModel::where('message_to', 4)->count();
    }

}
