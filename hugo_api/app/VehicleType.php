<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class VehicleType extends Eloquent
{
    protected $collection = 'vehicle_type';

    protected $fillable = [
        "id","name","AmountType","changing", "unit"
    ];
}
