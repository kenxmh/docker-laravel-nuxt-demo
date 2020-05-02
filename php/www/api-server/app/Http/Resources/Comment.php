<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Comment extends JsonResource
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
            'is_author'  => $this->is_author,
            'nickname'   => $this->nickname,
            'content'    => $this->content,
            'created_at' => (string) $this->created_at,
        ];
    }
}
