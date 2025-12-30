<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'id',
        'client_id',
        'client_secret',
        'name',
        'allowed_redirect_uris',
        'audience',
        'status',
    ];

    protected $casts = [
        'allowed_redirect_uris' => 'array',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
