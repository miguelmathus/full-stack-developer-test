<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Journal extends Eloquent
{
    protected $collection = 'journals';

    protected $fillable = [ "numberPlate", "checkin", "checkout", "status", "user","updatedBy"];
}
