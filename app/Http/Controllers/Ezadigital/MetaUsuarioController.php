<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 04/09/2020
 * Time: 3:23 PM
 */

namespace App\Http\Controllers\Ezadigital;

use App\Ezadigital\Meta;
use App\Ezadigital\MetaUsuario;
use Illuminate\Support\Facades\Input;

class MetaUsuarioController extends \App\Http\Controllers\Controlador {

    protected $modelo = 'MetaUsuario';

    public $subdirectorio = 'Ezadigital';


    public function antesDeValidar() {
        if (!$this->validarMeta()) {
            return false;
        }

        if (!$this->validarValor()) {
            return false;
        }

        if (!$this->validarFecha()) {
            return false;
        }

        return true;
    }
    
    
    public function antesDeGuardar() {
    	return true;
    }


    public function indexGet() {
        $id_usuario = (int)Input::get('id_usuario');

        return MetaUsuario::dataParaUsuario($id_usuario);
    }


    public function cargarMetasGet() {
        return Meta
            ::get([
                'id',
                'nombre',
            ]);
    }


    private function validarMeta() {
        $id_meta = (int)Input::get('id_meta');

        if (!$id_meta) {
            $this->retornarError('No se ha seleccionado la meta.', 'id_meta');
            return false;
        }

        return true;
    }


    private function validarValor() {
        $valor = Input::get('valor');

        if (empty($valor)) {
            $this->retornarError('No se ha especificado un valor.', 'valor');
            return false;
        }

        if (!is_numeric($valor)) {
            $this->retornarError('El valor no es un número válido.', 'valor');
            return false;
        }

        return true;
    }


    private function validarFecha() {
        Input::merge([
            'fecha' => date('d/m/Y'),
        ]);

        return true;
    }
}
