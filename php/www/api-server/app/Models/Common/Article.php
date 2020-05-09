<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
class Article extends Model
{
    public $table = 'article';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['title', 'body'];

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function categories()
    {
        return $this->belongsToMany('App\Models\Common\Category', 'article_category', 'article_id', 'category_id');
    }


}
