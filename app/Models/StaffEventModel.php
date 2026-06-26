<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class StaffEventModel extends Model
{
    use HasFactory;

    protected $table = 'staff_events';

    protected $fillable = [
        'school_id', 'title', 'description', 'event_date', 'start_time', 'end_time',
        'event_type', 'custom_event_type_id', 'location', 'created_by',
    ];

    protected $hidden = ['is_delete'];

    /** Types prédéfinis (enum SQL) — conservés pour rétrocompatibilité */
    public static array $typeLabels = [
        'academic'       => 'Académique',
        'cultural'       => 'Culturel',
        'administrative' => 'Administratif',
        'exam'           => 'Examen',
        'ceremony'       => 'Cérémonie',
        'trip'           => 'Sortie scolaire',
        'custom'         => 'Personnalisé',
    ];

    public static array $typeColors = [
        'academic'       => '#3b82f6',
        'cultural'       => '#8b5cf6',
        'administrative' => '#f59e0b',
        'exam'           => '#ef4444',
        'ceremony'       => '#10b981',
        'trip'           => '#06b6d4',
        'custom'         => '#6366f1',
    ];

    // ── Relation vers le type personnalisé ────────────────────────────────────

    public function customType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EventTypeCustomModel::class, 'custom_event_type_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function getSingle(int $id): ?self
    {
        return self::find($id);
    }

    /**
     * Liste paginée avec scoping école.
     */
    public static function getAll(int $perPage)
    {
        $user         = Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = self::select(
                'staff_events.*',
                'users.name as creator_name',
                'users.last_name as creator_last_name',
                'event_type_customs.name as custom_type_name',
                'event_type_customs.color as custom_type_color',
            )
            ->leftJoin('users', 'users.id', '=', 'staff_events.created_by')
            ->leftJoin('event_type_customs', 'event_type_customs.id', '=', 'staff_events.custom_event_type_id')
            ->where('staff_events.is_delete', 0);

        // Scoping multi-tenant
        if (! $isSuperAdmin && $user) {
            $q->where('staff_events.school_id', $user->school_id);
        }

        if ($v = Request::get('event_type')) $q->where('staff_events.event_type', $v);
        if ($v = Request::get('date_from'))  $q->whereDate('staff_events.event_date', '>=', $v);
        if ($v = Request::get('date_to'))    $q->whereDate('staff_events.event_date', '<=', $v);

        return $q->orderBy('staff_events.event_date', 'desc')->paginate($perPage);
    }

    /**
     * Prochains événements — scoping école de l'utilisateur connecté.
     */
    public static function getUpcoming(int $limit = 5)
    {
        $user         = Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = self::where('is_delete', 0)->whereDate('event_date', '>=', today());

        if (! $isSuperAdmin && $user) {
            $q->where('school_id', $user->school_id);
        }

        return $q->orderBy('event_date')->limit($limit)->get();
    }

    /**
     * Événements pour le calendrier — scoped par école de l'utilisateur connecté.
     */
    public static function getCalendarEvents(): array
    {
        $user         = Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = self::with('customType')
            ->where('is_delete', 0)
            ->whereDate('event_date', '>=', today()->subMonth())
            ->whereDate('event_date', '<=', today()->addMonths(3));

        // Scoping : apprenant, parent, prof ne voient que les événements de leur école
        if (! $isSuperAdmin && $user) {
            $q->where('school_id', $user->school_id);
        }

        return $q->orderBy('event_date')
            ->get()
            ->map(function ($event) {
                // Si type personnalisé → utiliser ses infos, sinon les prédéfinis
                $label = $event->customType
                    ? $event->customType->name
                    : (self::$typeLabels[$event->event_type] ?? $event->event_type);
                $color = $event->customType
                    ? $event->customType->color
                    : (self::$typeColors[$event->event_type] ?? '#6366f1');

                return [
                    'id'    => $event->id,
                    'title' => $event->title,
                    'start' => $event->event_date,
                    'color' => $color,
                    'extendedProps' => [
                        'type'        => $event->event_type,
                        'type_label'  => $label,
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
