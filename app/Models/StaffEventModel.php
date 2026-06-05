<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class StaffEventModel extends Model
{
    use HasFactory;

    protected $table = 'staff_events';

    protected $fillable = [
        'title', 'description', 'event_date', 'start_time', 'end_time',
        'event_type', 'location', 'created_by',
    ];

    protected $hidden = ['is_delete'];

    public static array $typeLabels = [
        'academic'       => 'Académique',
        'cultural'       => 'Culturel',
        'administrative' => 'Administratif',
        'exam'           => 'Examen',
        'ceremony'       => 'Cérémonie',
        'trip'           => 'Sortie scolaire',
    ];

    public static array $typeColors = [
        'academic'       => '#3b82f6',
        'cultural'       => '#8b5cf6',
        'administrative' => '#f59e0b',
        'exam'           => '#ef4444',
        'ceremony'       => '#10b981',
        'trip'           => '#06b6d4',
    ];

    public static function getSingle(int $id): ?self
    {
        return self::find($id);
    }

    public static function getAll(int $perPage)
    {
        $q = self::select('staff_events.*', 'users.name as creator_name', 'users.last_name as creator_last_name')
            ->leftJoin('users', 'users.id', '=', 'staff_events.created_by')
            ->where('staff_events.is_delete', 0);

        if ($v = Request::get('event_type')) $q->where('staff_events.event_type', $v);
        if ($v = Request::get('date_from'))  $q->whereDate('staff_events.event_date', '>=', $v);
        if ($v = Request::get('date_to'))    $q->whereDate('staff_events.event_date', '<=', $v);

        return $q->orderBy('staff_events.event_date', 'desc')->paginate($perPage);
    }

    public static function getUpcoming(int $limit = 5)
    {
        return self::where('is_delete', 0)
            ->whereDate('event_date', '>=', today())
            ->orderBy('event_date')
            ->limit($limit)
            ->get();
    }

    public static function getCalendarEvents(): array
    {
        return self::where('is_delete', 0)
            ->whereDate('event_date', '>=', today()->subMonth())
            ->whereDate('event_date', '<=', today()->addMonths(3))
            ->orderBy('event_date')
            ->get()
            ->map(function ($event) {
                return [
                    'id'    => $event->id,
                    'title' => $event->title,
                    'start' => $event->event_date,
                    'color' => self::$typeColors[$event->event_type] ?? '#6366f1',
                    'extendedProps' => [
                        'type'        => $event->event_type,
                        'type_label'  => self::$typeLabels[$event->event_type] ?? $event->event_type,
                        'description' => $event->description,
                        'location'    => $event->location,
                        'start_time'  => $event->start_time,
                        'end_time'    => $event->end_time,
                    ],
                ];
            })
            ->toArray();
    }
}
