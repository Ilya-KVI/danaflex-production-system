<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionLine extends Model
{

    protected $fillable = [

        'name',

        'temperature',

        'load_percent',

        'material',

        'status'

    ];



    public function orders()
    {

        return $this->hasMany(
            ProductionOrder::class
        );

    }

}
