<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetSucursal extends \App\Modelo
{
    protected $table = 'intranet_sucursal';
    protected $fillable = [     
        'nombre',
        'codigo'
    ];

}
