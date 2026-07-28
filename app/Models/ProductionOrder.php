<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{

    protected $fillable = [

        'customer_name',

        'material',

        'quantity',

        'status',

        'production_line_id'

    ];



    public function productionLine()
    {

        return $this->belongsTo(
            ProductionLine::class
        );

    }

}
