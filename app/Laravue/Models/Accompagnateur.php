<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Accompagnateur extends Model
{
    //
    protected $fillable = ['nomArabe','prenomArabe','sexe', 'telephoneTunisien','image','telephoneEtranger','user_id','package_id'];
    
    public function package()
    {
    	return $this->belongsTo('App\Laravue\Models\Package');
    }

}
