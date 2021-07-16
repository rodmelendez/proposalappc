<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IntranetBarrio;
use App\IntranetPais;
use App\IntranetDepartamento;
use App\IntranetMunicipio;
use Illuminate\Support\Facades\Input;

class IntranetBarrioController extends Controlador
{

    protected $modelo = 'IntranetBarrio';
    public function cargarListadosGet() {
        $paises = IntranetPais::all();
        
        foreach ($paises as $pais ) {
            $pais->departamentos = IntranetDepartamento::where('id_pais','=',$pais->id)->get();
            foreach($pais->departamentos as $departamento){
                $departamento->municipios = IntranetMunicipio::where('id_departamento','=',$departamento->id)->get();
            }
        }
       
       
        $item = 'paises';
         $this->especificarRespuesta($item, $paises);
         return $this->retornar();
    }

    public static function catalogo(){
        $paises = IntranetPais::all();
        
        foreach ($paises as $pais ) {
            $pais->departamentos = IntranetDepartamento::where('id_pais','=',$pais->id)->get();
            foreach($pais->departamentos as $departamento){
                $departamento->municipios = IntranetMunicipio::where('id_departamento','=',$departamento->id)->get();
            }
        }
        return $paises;
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
