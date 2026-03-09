<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
   use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
      'name',
      'email',
      'password',
      'phone',
      'role_id',
      'saldo_balance',
      'affiliate_saldo'
   ];

   /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
   protected $hidden = [
      'password',
      'remember_token',
      'email_verified_at',
      'updated_at',
      // 'created_at'
   ];

   /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
   protected $casts = [
      'email_verified_at' => 'datetime',
      'is_primary' => 'boolean'
   ];

   protected $with = ['address'];

   public $appends = ['is_admin', 'permissions'];

   public function address()
   {
      return $this->hasMany(UserAddress::class);
   }

   public function role()
   {
      return $this->belongsTo(Role::class);
   }
   public function affiliate()
   {
      return $this->belongsTo(Affiliate::class);
   }

   public function getIsAdminAttribute()
   {
      if ($this->role_id) {
         return true;
      }
      return false;
   }

   public function license()
   {
      return $this->belongsToMany(Product::class, 'licenses', 'user_id', 'product_id');
   }

   public function getPermissionsAttribute()
   {
      return Cache::remember('user_permissions_' . $this->id, now()->addSeconds(5), function () {
         return self::getUserPermissions($this->id);
      });
   }

   public static function getUserPermissions($id = null)
   {
      $userId = $id ??(Auth::check() ? Auth::id() : null);
      if (!$userId) return [];
      return DB::table('permissions')
         ->select(
            'permissions.slug as permission_name'
         )
         ->join('permission_role', 'permission_role.permission_id', 'permissions.id')
         ->join('roles', 'roles.id', 'permission_role.role_id')
         ->join('users', 'users.role_id', 'roles.id')
         ->where('users.id', $userId)
         ->groupBy('permission_name')
         ->get()->pluck('permission_name')->toArray();
   }
}
