<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Invoice extends Eloquent
{
    protected $collection = 'invoice';

    protected $fillable = [ "vehicle","status", "validity", "minutes", "amount", "detail"];
}
