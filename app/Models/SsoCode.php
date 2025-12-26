<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsoCode extends Model
{
    protected $table = 'sso_codes';

    protected $fillable = [
        'id',
        'code',
        'user_id',
        'company_id',
        'role',
        'client_id',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
