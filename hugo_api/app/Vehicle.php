<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Vehicle extends Eloquent
{
    protected $collection = 'vehicle';

    protected $fillable = [
        "type","numberPlate","status"
    ];

    public $rules = [
        "type" => "required",
        "numberPlate"=>"required",
        "status"=>"required"
    ];
}
