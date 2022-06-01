<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Paiement extends Model
{
    //
    protected $fillable = ['type','etat','montant','description','user_id','package_id'];


   /**
     * obtenir le package d'un paiement.
     */
    public function package()
    { 
        return $this->belongsTo(Package::class); 
    }

     /**
     * obtenir le user d'un paiement.
     */
    public function user()
    { 
        return $this->belongsTo(User::class); 
    }
}
