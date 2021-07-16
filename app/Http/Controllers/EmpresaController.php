<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 26/4/2019
 * Time: 9:50 PM
 */

namespace App\Http\Controllers;


use App\Departamento;
use App\Empleado;
use App\Empresa;
use App\Subdepartamento;
use App\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EmpresaController extends Controlador {

    protected $modelo = 'Empresa';


    /**
     * Después de guardar, se actualiza la foto si es el caso
     *
     * @param \App\Persona $item
     */
    public function despuesDeGuardar($item) {
        ImagenController::subirImagenParaItem($item, 'logo');

        /*//teléfonos
        $telefonos = Input::get('telefonos', []);
        $item->guardarTelefonos($telefonos);

        //correos
        $correos = Input::get('correos', []);
        $item->guardarCorreos($correos);*/
    }


    public static function activa($id_empresa = null, $solo_id = false) {
        if ($id_empresa === null) {
            $id_empresa = session('id_empresa', false);

            if ($id_empresa === false) {
                if (!($usuario = Auth::user())) {
                    return null;
                }

                if (!($persona = $usuario->traerPersona())) {
                    return null;
                }

                if (!($empleado = Empleado::where('id_persona', '=', (int)$persona->id)->first())) {
                    return null;
                }

                $id_empresa = $empleado->id_empresa;
            }
        }

        session(['id_empresa' => $id_empresa]);

        if (!$solo_id) {
            return Empresa::find((int)$id_empresa);
        }

        return $id_empresa;
    }


    public static function urlLogo($nombre_foto = null, $tamano = 's', $defecto = null) {
        $avatar_defecto = $defecto ?? asset('img/img-placeholder.png');
        if (!empty($tamano)) $tamano = '/' . $tamano;
        return !empty($nombre_foto) ? asset(config('app.uploads_img_dir') . $tamano . '/' . $nombre_foto) : $avatar_defecto;
    }


    public function cargarEstructuraGet() {
        $empresas = Empresa::get(['id', 'nombre'])->toArray();
        $sucursales = Sucursal::get(['id', 'id_empresa', 'nombre'])->toArray();
        $departamentos = Departamento::get(['id', 'id_sucursal', 'nombre'])->toArray();
        $sub_departamentos = Subdepartamento::get(['id', 'id_departamento', 'nombre'])->toArray();

        $this->especificarRespuesta('empresas', $empresas);
        $this->especificarRespuesta('sucursales', $sucursales);
        $this->especificarRespuesta('departamentos', $departamentos);
        $this->especificarRespuesta('sub_departamentos', $sub_departamentos);
        return $this->retornar();
    }

}