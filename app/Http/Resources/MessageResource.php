<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       // return parent::toArray($request);
       return [
        'id' => $this->id,
        'description' => $this->description,
        'reponseDescription' => $this->reponseDescription,
        'user_id' => $this->user_id,
        'user' =>$this->user->name,
        'etat' => $this->etat,
       ];
    }
}

