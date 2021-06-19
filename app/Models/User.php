<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Olympus\Comment;
use App\Models\Shop\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
  use HasRoles;
  use HasApiTokens;
  use HasFactory;
  use HasProfilePhoto;
  use HasTeams;
  use Notifiable;
  use TwoFactorAuthenticatable;

  protected $table = 'users';
  protected $guarded = ['id'];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
    'two_factor_recovery_codes',
    'two_factor_secret',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'mobile_phone_verified_at' => 'datetime',
  ];

  /**
   * -----------------------------------------
   *	Relations
   * -----------------------------------------
   */

  public function comments()
  {
    return $this->hasMany(Comment::class, 'user_id', 'id');
  }

  /**
   * 
   */
  public function orders()
  {
    return $this->hasMany(Order::class, 'customer_id', 'id');
  }
}
