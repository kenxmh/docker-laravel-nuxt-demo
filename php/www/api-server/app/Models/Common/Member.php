<?php

namespace App\Models\Common;

// use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public $table = 'member';

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
        return ['role' => 'member'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'nickname',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
}
