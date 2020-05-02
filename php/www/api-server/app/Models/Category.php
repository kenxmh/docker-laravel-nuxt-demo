<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $table = 'category';

    public function articles()
    {
        return $this->belongsToMany('App\Models\Article', 'article_category', 'category_id', 'article_id');
    }
}
