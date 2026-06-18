<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkAttachmentModel extends Model
{
    use HasFactory;

    protected $table = 'work_attachments';

    protected $fillable = [
        'work_id',
        'file_name',
        'file_path',
        'file_ext',
        'file_size',
        'is_delete',
    ];

    protected $hidden = ['is_delete'];

    /** URL publique du fichier */
    public function getUrlAttribute(): string
    {
        return url('upload/practicalworks/' . $this->file_path);
    }

    /** Taille lisible (ex: 1.2 Mo) */
    public function getReadableSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes < 1024) return $bytes . ' o';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' Ko';
        return round($bytes / 1048576, 1) . ' Mo';
    }

    public function work()
    {
        return $this->belongsTo(WorkModel::class, 'work_id');
    }

    public static function getByWork(int $workId)
    {
        return static::where('work_id', $workId)
            ->where('is_delete', 0)
            ->orderBy('id')
            ->get();
    }
}
