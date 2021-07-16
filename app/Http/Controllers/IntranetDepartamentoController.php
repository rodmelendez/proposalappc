<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IntranetDepartamento;
use App\IntranetPais;
use Illuminate\Support\Facades\Input;

class IntranetDepartamentoController extends Controlador
{

    protected $modelo = 'IntranetDepartamento';

    public function cargarListadosGet() {
        $paises = IntranetPais::all();
        $item = 'paises';
        $this->especificarRespuesta($item, $paises);
        return $this->retornar();
    }

    /** Validaciones **/
    public function antesdeValidar()
    {
        if (!$this->validarNombre()) {
            return false;
        }
        return true;
    }

    private function validarNombre()
    {
        $nombre = Input::get('nombre');
        if (empty($nombre)) {
            $this->retornarError('No se ha especificado un nombre.', 'nombre');
            return false;
        }

        if (is_numeric($nombre)) {
            $this->retornarError('El nombre no es un valor válido.', 'nombre');
            return false;
        }

        return true;
    }
}
