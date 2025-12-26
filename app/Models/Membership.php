<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'company_id',
        'role',
        'status',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
