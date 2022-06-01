<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PelerinResource extends JsonResource
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
        'nomArabe' => $this->nomArabe,
        'prenomArabe' => $this->prenomArabe,
        'dateNaissance' => $this->dateNaissance,
        'sexe' => $this->sexe,
        'telephoneTunisien' => $this->telephoneTunisien,
        'image' => $this->image,
        'numeroDePassport' => $this->numeroDePassport,
        'dateExpiration' => $this->dateExpiration,
        'dateEmission' => $this->dateEmission,
        'etat' => $this->etat,
        'user_id' =>$this->user_id,
        'package_id' =>$this->package_id,
        'labelle' =>$this->package->labelle,
    ];
    }
   
}
