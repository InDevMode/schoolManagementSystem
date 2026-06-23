<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingModel extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'school_name',
        'school_type',
        'address',
        'phone',
        'email',
        'uai_number',
        'logo',
        'favicon',
        'paypal_email',
        'kkiapay_public_key',
        'kkiapay_private_key',
        'kkiapay_secret_key',
        'stripe_public_key',
        'stripe_secret_key',
        'fedapay_public_key',
        'fedapay_secret_key',
        // Background de la page d'authentification (configurable par le super admin)
        'auth_bg_type',
        'auth_bg_value',
        'auth_bg_label',
        'auth_bg_overlay',
    ];

    /**
     * Retourne la configuration complète du background auth.
     * Fournit des valeurs par défaut si rien n'est défini.
     */
    public function getAuthBackground(): array
    {
        return [
            'type'    => $this->auth_bg_type    ?? 'gradient',
            'value'   => $this->auth_bg_value   ?? 'linear-gradient(145deg, #5b21b6 0%, #7c3aed 50%, #6d28d9 100%)',
            'label'   => $this->auth_bg_label   ?? null,
            'overlay' => $this->auth_bg_overlay ?? 'rgba(0,0,0,0.35)',
        ];
    }

    protected $hidden = [
        'is_delete',
    ];

    public static function getSingle(int $id)
    {
        return SettingModel::find($id);
    }

    public function getFavicon(): string
    {
        $path = public_path('upload/setting/' . $this->favicon);
        if (!empty($this->favicon) && file_exists($path)) {
            return url('upload/setting/' . $this->favicon);
        }
        // Image par défaut si rien n'existe
        return url('upload/favicon.png');
    }

    public function getLogo(): string
    {
        $path = public_path('upload/setting/' . $this->logo);
        if (!empty($this->logo) && file_exists($path)) {
            return url('upload/setting/' . $this->logo);
        }
        // Image par défaut si rien n'existe
        return url('upload/logo.png');
    }


}
