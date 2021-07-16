<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 24/11/2019
 * Time: 1:22 PM
 */

namespace App\Http\Controllers;


use App\Empleado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class UsuarioTouchIdController extends Controlador {

    protected $modelo = 'UsuarioTouchId';


    public function antesDeGuardar() {
        $usuario = Auth::user();

        if (!($empleado = Empleado::find((int)Input::get('id_empleado')))) {
            $this->retornarError(self::ERROR_NO_ENCONTRADO);
            return false;
        }

        $id_usuario = null;

        if ($persona = $empleado->persona) {
            $id_usuario = $persona->idUsuario();
        }

        Input::merge([
            'id_usuario_creacion' => $usuario ? $usuario->id : null,
            'id_usuario' => $id_usuario ? $id_usuario : null,
        ]);

        return true;
    }

}