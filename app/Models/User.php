<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Laravel\Passport\HasApiTokens;

/**
 * Class User for platform users
 * @package App\Models
 */

class User extends Authenticatable
{
    use Notifiable;
    // use SoftDeletes;
    use HasRoles;
    use HasFactory;
    use HasApiTokens;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
//        'name', 'email', 'password', 'referral_from', 'role', 'verify_level', 'email_verified_level',
        'email', 'password', 'password_recovery', 'referral_from', 'menuroles', 'verify_level', 'is_deleted', 'status', 'email_verified_at', 'phone_verified_at', 'video_verified_at', 'auth_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $attributes = [
        'menuroles' => 'user',
    ];
}
