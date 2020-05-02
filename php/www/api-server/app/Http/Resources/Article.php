<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;
use Parsedown;

class Article extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $parsedown = new Parsedown();
        $parsedown->setBreaksEnabled(true);
        return [
            'uuid'       => Hashids::encode(2, $this->id),
            'title'      => $this->title,
            'body'       => $parsedown->text($this->body),
            'views'      => $this->views,
            'comments'   => $this->comments,
            'sort'       => $this->sort,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'categories' => \App\Http\Resources\Category::collection($this->categories),
        ];
    }
}
