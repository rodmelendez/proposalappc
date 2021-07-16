<?php

namespace App\Http\Controllers;

use App\IntranetEmpresa;
use App\IntranetCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class IntranetEmpresaController extends Controlador
{

    protected $modelo = 'IntranetEmpresa';
    public function cargarListadosGet() 
    {
        $clientes = IntranetCliente::all();
        $item = 'clientes';
        $this->especificarRespuesta($item,$clientes);
        return $this->retornar();
     }


     
    public function itemDataAdicional($item) 
    {
        //cliente
        $cliente = $item->cliente()->first();

        $this->especificarRespuesta('cliente', $cliente); 

    }
    public static function apiRegistrar($Empresa)
    {    
        $validarEmpresa = new IntranetEmpresa;
        $validacionEmpresa = Validator::make(
            $Empresa,
             $validarEmpresa->reglasValidacion(null,0)
         );
        if($validacionEmpresa->passes()){
            $nuevo = IntranetEmpresa::create($Empresa);
            if(!$nuevo){
                return false;
            }
            return $nuevo;
        }
        return false;
    }

    /************************************************/
    /*Modificado Rodolfo, 14 de mayo*/
    public function antesdeValidar()
    {
        if (!$this->validarNombre()) {
            return false;
        }
        return true;
    }

    /*public function antesDeGuardar()
    {

    }*/

    private function validarNombre()
    {
        $nombre = Input::get('nombre');
        if (empty($nombre)) {
            $this->retornarError('No se ha especificado el nombre de una Empresa.', 'nombre');
            return false;
        }

        if (is_numeric($nombre)) {
            $this->retornarError('El nombre no puede ser numerico.', 'nombre');
            return false;
        }

        return true;
    }


}