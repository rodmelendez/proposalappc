<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\IntranetDireccion;
use Illuminate\Support\Facades\Validator;

class IntranetDireccionController extends Controlador
{

    protected $modelo = 'IntranetDireccion';

    public static function apiRegistrar($direccion)
    {    
        $validardireccion = new IntranetDireccion;
        $validaciondireccion = Validator::make(
            $direccion,
             $validardireccion->reglasValidacion(null,0)
         );
        if($validaciondireccion->passes()){
            $nuevo = IntranetDireccion::create($direccion);
            if(!$nuevo){
                return false;
            }
            return $nuevo;
        }
        return false;
    }

    public static function apiEditar($direccion, $id)
    {
        //dd($except);
        $validardireccion = new IntranetDireccion;
        $validaciondireccion = Validator::make(
            $direccion,
            $validardireccion->reglasValidacion(null,0)
        );
        if($validaciondireccion->passes()){
            $nuevo = IntranetDireccion::where('id', $id)
                ->update($direccion);
            if(!$nuevo){
                return false;
            }
            return $nuevo;
        }
        return false;
    }
}
