<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $fillable = ['description','reponseDescription','expediteur_id', 'receveur_id','etat'];
  
     /**
     * obtenir le utulisateur qui a envoyer le message .
     */
    public function user()
    { 
        return $this->belongsTo(User::class); 
    }
    
}
