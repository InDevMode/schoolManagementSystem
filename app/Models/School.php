<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class School extends Model
{
    protected $table = 'schools';

    protected $fillable = [
        'school_name',
        'school_type',
        'school_code',
        'address',
        'phone',
        'email',
        'uai_number',
        'logo',
        'favicon',
        'paypal_email',
        'paypal_client_id',
        'paypal_secret',
        'paypal_mode',
        'kkiapay_public_key',
        'kkiapay_private_key',
        'kkiapay_secret_key',
        'stripe_public_key',
        'stripe_secret_key',
        'fedapay_public_key',
        'fedapay_secret_key',
        'academic_year',
        'period_type',
        'status',
        'is_delete',
        'created_by',
    ];

    protected $hidden = [
        'kkiapay_private_key',
        'kkiapay_secret_key',
        'stripe_secret_key',
        'fedapay_secret_key',
        'is_delete',
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    /** Tous les utilisateurs appartenant à cette école */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'school_id');
    }

    /** Admins de cette école */
    public function admins(): HasMany
    {
        return $this->hasMany(User::class, 'school_id')
            ->where('user_type', 1)
            ->where('is_delete', 0);
    }

    // ── Accesseurs ─────────────────────────────────────────────────────────

    public function getLogoUrl(): string
    {
        $path = public_path('upload/school/' . $this->logo);
        if (!empty($this->logo) && file_exists($path)) {
            return url('upload/school/' . $this->logo);
        }
        return url('upload/logo.png');
    }

    public function getFaviconUrl(): string
    {
        $path = public_path('upload/school/' . $this->favicon);
        if (!empty($this->favicon) && file_exists($path)) {
            return url('upload/school/' . $this->favicon);
        }
        return url('upload/favicon.png');
    }

    // ── Helpers statiques ──────────────────────────────────────────────────

    public static function generateCode(string $name): string
    {
        $base = Str::slug($name);
        $code = $base;
        $i    = 1;
        while (static::where('school_code', $code)->exists()) {
            $code = $base . '-' . $i++;
        }
        return $code;
    }

    /** Récupère l'école de l'utilisateur connecté ou null si super admin */
    public static function forCurrentUser(): ?self
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user || $user->user_type === 0) {
            return null;
        }
        return static::find($user->school_id);
    }

    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_delete', 0)
            ->where('status', 1)
            ->orderBy('school_name')
            ->get();
    }
}
