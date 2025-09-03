<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingModel extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'paypal_email',
        'kkiapay_public_key',
        'kkiapay_private_key',
        'kkiapay_secret_key',
        'stripe_public_key',
        'stripe_secret_key',
    ];

    protected $hidden = [
        'is_delete',
    ];

    public static function getSingle(int $id)
    {
        return SettingModel::find($id);
    }

    public function getFavicon(): string
    {
        $path = base_path('upload/setting/' . $this->favicon);
        if (!empty($this->favicon) && file_exists($path)) {
            return url('upload/setting/' . $this->favicon);
        }
        // Image par défaut si rien n'existe
        return url('upload/favicon.png');
    }

    public function getLogo(): string
    {
        $path = base_path('upload/setting/' . $this->logo);
        if (!empty($this->logo) && file_exists($path)) {
            return url('upload/setting/' . $this->logo);
        }
        // Image par défaut si rien n'existe
        return url('upload/logo.png');
    }


}
