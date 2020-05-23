<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class RegisteredVehicle extends Eloquent
{
    protected $collection = 'registered_vehicle';

    protected $fillable = [
        "ownerFirstName", "ownerSecondName", "ownerLastname", "ownerSecondLastname"
    ];
}
