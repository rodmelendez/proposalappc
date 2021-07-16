<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IntranetContacto;
use Illuminate\Support\Facades\Validator;

class IntranetContactoController extends Controlador
{

    protected $modelo = 'IntranetContacto';

    public static function apiRegistrar($contacto)
    {    
        $validarcontacto = new IntranetContacto;
        $validacioncontacto = Validator::make(
            $contacto,
             $validarcontacto->reglasValidacion(null,0)
         );
        if($validacioncontacto->passes()){
            $nuevo = IntranetContacto::create($contacto);
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
        $validardireccion = new IntranetContacto();
        $validaciondireccion = Validator::make(
            $direccion,
            $validardireccion->reglasValidacion(null,0)
        );
        if($validaciondireccion->passes()){
            $nuevo = IntranetContacto::where('id', $id)
                ->update($direccion);
            if(!$nuevo){
                return false;
            }
            return $nuevo;
        }
        return false;
    }
}
