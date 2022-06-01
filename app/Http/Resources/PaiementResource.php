<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaiementResource extends JsonResource
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
        'type' => $this->type,
        'montant' => $this->montant,
        'etat' => $this->etat,
        'description' => $this->description,
        'user_id' =>$this->user_id,
        'package_id' =>$this->package_id,
        'package' =>$this->package->labelle,
        'user' =>$this->user->name,
    ];
    }
}
