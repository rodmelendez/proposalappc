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

    public static function traerData() {
        $campos = [
            'id',
            'codigo',
            'nombre',
        ];

        return self::orderBy('nombre')->get($campos)->toArray();
    }

    //una sucursal tiene muchas presolicitudes
    public function presolicitudes(){
        return $this->hasMany('App\IntranetPresolicitud', 'id_sucursal', 'id');
    }
}
