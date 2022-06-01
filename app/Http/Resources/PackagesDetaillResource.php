<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackagesDetaillResource extends JsonResource
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
            'image' =>$this->image,
            'id' =>$this->id,
            'labelle' =>$this->labelle,
            'description' =>$this->description,
            'prix' =>$this->prix,
            'NombrePlace' =>$this->NombrePlace,
            'NombreAcc' =>$this->NombreAcc,
            'dateDepart' =>$this->dateDepart,
            'NombrePlaceRestant' =>$this->NombrePlaceRestant,
            'pelerins' =>$this->pelerins,
        ];
    }
}
