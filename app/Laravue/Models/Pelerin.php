<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;


class Pelerin extends Model
{
    protected $guarded = [];
    protected $fillable = ['nomArabe','prenomArabe','dateNaissance','sexe', 'telephoneTunisien','image','numeroDePassport','dateExpiration','dateEmission','user_id','createur_id','etat','package_id'];
    
    public function package()
    {
    	return $this->belongsTo('App\Laravue\Models\Package');
    }
}