<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 30/4/2019
 * Time: 10:30 AM
 */

namespace App\Http\Controllers;


use App\Credencial;
use App\CredencialCategoria;
use Illuminate\Support\Facades\Input;

class CredencialController extends Controlador {

    protected $modelo = 'Credencial';


    public function antesDeGuardar() {
        $this->antesDeGuardarDefecto();

        //contraseña
        {
            $credencial = new Credencial;
            $contrasena = Input::get('contrasena');
            Input::merge([
                'contrasena' => $credencial->encriptar($contrasena),
            ]);
        }

        return true;
    }


    public function despuesDeGuardar($item) {
        ImagenController::subirImagenParaItem($item, 'imagen');
    }


    public function itemDataAdicional($item) {
        $credencial = new Credencial;
        $this->especificarRespuesta('contrasena', $credencial->desencriptar($item->contrasena));
    }


    public function cargarContrasenaGet() {
        $id_credencial = (int)Input::get('id');

        if (!($credencial = Credencial::find($id_credencial))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $contrasena = $credencial->desencriptar();

        $this->especificarRespuesta('contrasena', $contrasena);
        return $this->retornar();
    }


    public function listaParametrosGet() {
        $credencial_categorias = CredencialCategoria
            ::orderBy('nombre')
            ->get(['id', 'nombre']);

        $this->especificarRespuesta('credencial_categorias', $credencial_categorias);
        return $this->retornar();
    }

}