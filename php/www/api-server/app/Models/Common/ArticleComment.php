<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    public $table = 'article_comment';

    public function article()
    {
        return $this->belongsTo('App\Models\Common\Article');
    }

    public function quote()
    {
        return $this->belongsTo('App\Models\Common\ArticleComment', 'quote_id','id');
    }
}
