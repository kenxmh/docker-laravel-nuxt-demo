<?php

namespace App\Models\Admin;

// use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public $table = 'admin_user';
    
    const STATUS_ACTIVE  = 0;
    const STATUS_RELOGIN = 1;
    const STATUS_BANNED  = 2;
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * 获取会储存到 jwt 声明中的标识，一般用表的主键字段名，即id
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * 包含到 jwt 声明中的自定义键值对数组，用于多表的验证隔离
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return ['role' => 'admin'];
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'realname',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\Role', 'admin_user_role', 'admin_id', 'role_id');
    }
}
