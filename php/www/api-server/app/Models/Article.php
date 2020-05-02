<?php

namespace App\Models;

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
        return $this->belongsToMany('App\Models\Category', 'article_category', 'article_id', 'category_id');
    }

    // public function appendUUID()
    // {
    //     $this->uuid = Hashids::encode(config('constant.UUID_TYPE_ARTICLE'), $this->id);
    //     $this->save();
    // }

    // public function getUUID()
    // {
    //     return Hashids::encode(config('constant.UUID_TYPE_ARTICLE'), $this->id);
    // }

    // public function getImageUUID()
    // {
    //     return Hashids::encode(config('constant.UUID_TYPE_ARTICLE_IMAGE'), $this->id);
    // }


}
