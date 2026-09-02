<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * UploadService — Centralise tous les uploads vers Supabase Storage.
 *
 * Organisation des buckets/dossiers :
 *   schoolms/profiles/          → photos de profil utilisateurs
 *   schoolms/schools/           → logos et favicons des écoles
 *   schoolms/settings/          → logo/favicon des paramètres globaux
 *   schoolms/works/             → pièces jointes travaux (admin/prof)
 *   schoolms/homeworks/         → soumissions de devoirs (apprenants)
 *   schoolms/chats/             → fichiers partagés dans le chat
 *
 * Disque utilisé : 'supabase' (config/filesystems.php)
 */
class UploadService
{
    private const DISK = 'supabase';

    // ── Upload ────────────────────────────────────────────────────────────────

    /**
     * Upload un fichier et retourne son path relatif dans le bucket.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder    ex: 'profiles', 'schools', 'works'
     * @param  string        $prefix    ex: 'admin', 'student', 'school_logo'
     * @return string  Path stocké en DB, ex: "profiles/admin20250101abc.jpg"
     *
     * @throws \RuntimeException si l'upload échoue
     */
    public static function upload(UploadedFile $file, string $folder, string $prefix = 'file'): string
    {
        $ext      = strtolower($file->getClientOriginalExtension());
        $fileName = $prefix . '_' . date('dmYHis') . '_' . Str::random(8) . '.' . $ext;
        $path     = $folder . '/' . $fileName;

        Storage::disk(self::DISK)->put($path, file_get_contents($file->getRealPath()), 'public');

        return $path;
    }

    /**
     * Upload une pièce jointe en conservant le nom original.
     * Retourne un tableau avec path, nom original, extension et taille.
     */
    public static function uploadAttachment(UploadedFile $file, string $folder, string $prefix = 'attachment'): array
    {
        $originalName = $file->getClientOriginalName();
        $ext          = strtolower($file->getClientOriginalExtension());
        $fileSize     = $file->getSize();
        $slug         = Str::slug(mb_substr(pathinfo($originalName, PATHINFO_FILENAME), 0, 40));
        $fileName     = $prefix . '_' . date('dmYHis') . '_' . Str::random(8) . '_' . $slug . '.' . $ext;
        $path         = $folder . '/' . $fileName;

        Storage::disk(self::DISK)->put($path, file_get_contents($file->getRealPath()), 'public');

        return [
            'path'          => $path,
            'original_name' => $originalName,
            'ext'           => $ext,
            'size'          => $fileSize,
        ];
    }

    /**
     * Supprime un fichier dans Supabase Storage.
     * Silencieux si le fichier n'existe pas.
     */
    public static function delete(?string $path): void
    {
        if (!$path) return;

        try {
            Storage::disk(self::DISK)->delete($path);
        } catch (\Throwable $e) {
            Log::warning("UploadService::delete — Impossible de supprimer : {$path} — " . $e->getMessage());
        }
    }

    /**
     * Retourne l'URL publique d'un fichier stocké dans Supabase.
     *
     * @param  string|null  $path      Path stocké en DB, ex: "profiles/admin.jpg"
     * @param  string|null  $fallback  URL si path est vide
     */
    public static function url(?string $path, ?string $fallback = null): string
    {
        if (!$path) {
            return $fallback ?? asset('upload/default.jpg');
        }

        // Déjà une URL complète (données migrées avant ce service)
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk(self::DISK)->url($path);
    }

    // ── Helpers dossiers ─────────────────────────────────────────────────────

    public static function profileFolder(): string    { return 'profiles'; }
    public static function schoolFolder(): string     { return 'schools'; }
    public static function settingFolder(): string    { return 'settings'; }
    public static function worksFolder(): string      { return 'works'; }
    public static function homeworksFolder(): string  { return 'homeworks'; }
    public static function chatsFolder(): string      { return 'chats'; }
}
