<?php

namespace App\Services;

use App\Models\ClassModel;
use App\Models\PeriodModel;
use App\Models\SubjectModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * RefDataCache — Cache centralisé pour les listes de référence académique.
 *
 * Ces données (classes, matières, périodes) changent rarement mais sont lues
 * sur presque chaque page. Sans cache, chaque navigation provoque 2–3 aller-
 * retours réseau vers Supabase (Frankfurt) inutilement.
 *
 * Stratégie :
 *  - Clé par school_id (ou "global" pour le super admin)
 *  - TTL 10 min — suffisant pour absorber les rafales, court pour rester frais
 *  - Invalidation explicite à chaque create/update/delete dans les contrôleurs
 *
 * Usage :
 *   RefDataCache::classes()          → Collection des classes de l'école courante
 *   RefDataCache::subjects()         → Collection des matières
 *   RefDataCache::periods()          → Collection des périodes actives
 *   RefDataCache::currentPeriod()    → Première période courante ou null
 *   RefDataCache::forgetSchool($id)  → Invalider tout pour une école
 */
class RefDataCache
{
    /** TTL en secondes (10 minutes) */
    private const TTL = 600;

    // ──────────────────────────────────────────────────────────────────────────
    // API publique
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Classes actives de l'école de l'utilisateur connecté.
     */
    public static function classes(?int $schoolId = null): \Illuminate\Support\Collection
    {
        $key = self::key('classes', $schoolId);
        return Cache::remember($key, self::TTL, fn () => ClassModel::getClass($schoolId));
    }

    /**
     * Matières actives (globales — non scopées par école pour l'instant,
     * les matières ne sont pas multi-tenant dans ce schéma).
     */
    public static function subjects(): \Illuminate\Support\Collection
    {
        return Cache::remember('ref.subjects', self::TTL, fn () => SubjectModel::getSubject());
    }

    /**
     * Toutes les périodes actives de l'école de l'utilisateur connecté.
     */
    public static function periods(?int $schoolId = null): \Illuminate\Support\Collection
    {
        $key = self::key('periods', $schoolId);
        return Cache::remember($key, self::TTL, fn () => PeriodModel::getAllPeriods());
    }

    /**
     * Période courante (premier résultat) ou null.
     */
    public static function currentPeriod(?int $schoolId = null): ?PeriodModel
    {
        $key = self::key('current_period', $schoolId);
        return Cache::remember($key, self::TTL, fn () => PeriodModel::getCurrentPeriod()->first());
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Invalidation
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Invalider les classes d'une école (après create/update/delete de classe).
     */
    public static function forgetClasses(int $schoolId): void
    {
        Cache::forget(self::key('classes', $schoolId));
        // Le super admin voit toutes les classes (clé sans schoolId)
        Cache::forget('ref.classes.global');
    }

    /**
     * Invalider les matières (globales).
     */
    public static function forgetSubjects(): void
    {
        Cache::forget('ref.subjects');
    }

    /**
     * Invalider les périodes d'une école (après create/update/delete/set-current).
     */
    public static function forgetPeriods(int $schoolId): void
    {
        Cache::forget(self::key('periods', $schoolId));
        Cache::forget(self::key('current_period', $schoolId));
        Cache::forget('ref.periods.global');
        Cache::forget('ref.current_period.global');
    }

    /**
     * Invalider toutes les entrées de référence d'une école (ex : lors de la suppression).
     */
    public static function forgetSchool(int $schoolId): void
    {
        self::forgetClasses($schoolId);
        self::forgetPeriods($schoolId);
        // Les matières ne sont pas scopées par école : pas d'invalidation ici
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers privés
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Construit une clé de cache avec ou sans schoolId.
     * Si $schoolId n'est pas fourni, on le déduit de l'utilisateur connecté.
     */
    private static function key(string $type, ?int $schoolId = null): string
    {
        if ($schoolId === null) {
            $user = Auth::user();
            if ($user && (int) $user->user_type !== 0) {
                $schoolId = $user->school_id;
            }
        }

        return $schoolId !== null
            ? "ref.{$type}.school.{$schoolId}"
            : "ref.{$type}.global";
    }
}
