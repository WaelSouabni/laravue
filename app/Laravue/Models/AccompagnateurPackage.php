<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class AccompagnateurPackage extends Model
{
    //
    protected $fillable = ['user_id', 'package_id','role','etat'];
    public function user()
    { 
        return $this->belongsTo('App\Laravue\Models\User'); 
    }
    public function package()
    { 
        return $this->belongsTo('App\Laravue\Models\Package'); 
    }
}
