<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    public $table = 'admin_access';

    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\Role', 'admin_role_access', 'access_id', 'role_id');
    }
}
