<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PelerinPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'nomArabe' => $this->nomArabe,
            'prenomArabe' => $this->prenomArabe,
            'numeroDePassport' => $this->numeroDePassport,
            'etat' => $this->etat,
        ];
    }
}
