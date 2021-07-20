<?php

namespace App\Http\Controllers;

use App\IntranetPresolicitudProducto;
use Illuminate\Http\Request;

class IntranetPresolicitudProductoController extends Controlador
{
    protected $modelo = 'IntranetPresolicitudProducto';

    /*** API ***/

    public static function productosGet()
    {
        $productos= IntranetPresolicitudProducto::all();

        return $productos;

    }
}
