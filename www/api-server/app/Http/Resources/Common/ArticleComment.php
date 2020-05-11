<?php

namespace App\Http\Resources\Common;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ArticleComment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid'       => Hashids::encode(env('UUID_TYPE_ARTICLE_COMMENT'), $this->id),
            'is_author'  => $this->is_author,
            'quote'      => null,
            'nickname'   => $this->nickname,
            'content'    => $this->content,
            'created_at' => (string) $this->created_at,
        ];
    }
}
