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

    public static function getSingle(int $id){
        return SettingModel::find($id);
    }

}
