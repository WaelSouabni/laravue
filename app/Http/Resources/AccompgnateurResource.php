<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccompgnateurResource extends JsonResource
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
            'sexe' => $this->sexe,
            'telephoneTunisien' => $this->telephoneTunisien,
            'telephoneEtranger' => $this->telephoneEtranger,
            'image' => $this->image,
            'etat' => $this->etat,
            'user_id' => $this->user_id,
            
        ];
    }
}
