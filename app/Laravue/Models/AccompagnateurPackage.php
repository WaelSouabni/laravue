<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class AccompagnateurPackage extends Model
{
    //
    protected $fillable = ['user_id', 'package_id','role','etat'];

}
