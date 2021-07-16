<?php

namespace App\Http\Controllers;

use App\IntranetPersona;
use Illuminate\Http\Request;
use App\IntranetCliente;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class IntranetPersonaController extends Controlador
{

    protected $modelo = 'IntranetPersona';

    public function cargarListadosGet() 
    {
        $clientes = IntranetCliente::all();
        $item = 'clientes';
        $this->especificarRespuesta($item,$clientes);
        return $this->retornar();
     }
    
    public function itemDataAdicional($item) {
        //cliente
        $cliente = $item->cliente()->first();
        $this->especificarRespuesta('cliente', $cliente); 
    }
    public static function apiRegistrar($Persona)
    {    
        $validarPersona = new IntranetPersona;
        $validacionPersona = Validator::make(
            $Persona,
             $validarPersona->reglasValidacion(null,0)
         );
        if($validacionPersona->passes()){
            $nuevo = IntranetPersona::create($Persona);
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
        if (!$this->validarPrimerNombre()) {
            return false;
        }

        if (!$this->validarPrimerApellido()) {
            return false;
        }
        return true;
    }

    /*public function antesDeGuardar()
    {

    }*/

    private function validarPrimerNombre()
    {
        $nombre = Input::get('primer_nombre');
        if (empty($nombre)) {
            $this->retornarError('No se ha especificado un primer nombre para Persona.', 'primer_nombre');
            return false;
        }

        if (is_numeric($nombre)) {
            $this->retornarError('El primer nombre no puede ser numerico.', 'primer_nombre');
            return false;
        }
        return true;
    }

    private function validarPrimerApellido()
    {
        $primerapellido = Input::get('primer_apellido');
        if (empty($primerapellido)) {
            $this->retornarError('No se ha especificado un primer apellido para Persona.', 'primer_apellido');
            return false;
        }

        if (is_numeric($primerapellido)) {
            $this->retornarError('El primer apellido no puede ser numerico.', 'primer_apellido');
            return false;
        }
        return true;
    }
 
}
