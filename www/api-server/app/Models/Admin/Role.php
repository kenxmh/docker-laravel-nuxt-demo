<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $table = 'admin_role';

    public function accesses()
    {
        return $this->belongsToMany('App\Models\Admin\Access', 'admin_role_access', 'role_id', 'access_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\Admin\User', 'admin_user_role', 'role_id', 'admin_id');
    }
}
