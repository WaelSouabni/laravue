<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    //
    protected $fillable = ['labelle','description','NombrePlace','NombrePlaceRestant','prix','dateDepart','image','NombreAcc'];
  
     /**
     * Get the Pelerins for the Package .
     */
    public function pelerins()
    {
        return $this->hasMany('App\Laravue\Models\Pelerin');
    }
          /**
     * obtenir les paiement d'un packages.
     */
    public function paiements()
    {
        
        return $this->hasMany('Paiement');
    }
}
