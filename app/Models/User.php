<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Client;
use App\Models\Membership;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Client membership (client_user)
     * SSO / 権限制御用
     */
    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'client_user',
            'user_id',
            'client_id'
        )->withPivot('role');
    }

    /**
     * 会社内メンバーシップ
     */
    public function memberships()
    {
        return $this->hasMany(Membership::class, 'user_id');
    }

    /**
     * 現在の会社でのメンバーシップを取得
     */
    public function currentMembership()
    {
        return $this->memberships()
            ->where('company_id', $this->company_id)
            ->where('status', 'active')
            ->first();
    }

    /**
     * 現在の会社でadmin権限を持っているか
     */
    public function isAdmin(): bool
    {
        $membership = $this->currentMembership();
        return $membership && $membership->role === 'admin';
    }
}
