<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 22/4/2019
 * Time: 10:21 AM
 */

namespace App\Http\Controllers;


use App\Empleado;
use App\Empresa;
use App\Http\Controllers\Ezadigital\SimicroCreditoController;
use App\Marca;
use App\Opcion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class AppController extends Controlador {

    /**
     * Carga la página principal del sistema
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mostrarMain() {
        $usuario = Auth::user();

        if (!$usuario) {
            self::sinPermiso();
        }

        $usuario->permisos;

        if ($persona = $usuario->persona()->first()) {
            if ($empleado = $persona->empleado()) {

                $empresas = $empleado->empresas()->get()->toArray();
            }
        }

        //opciones
        $opciones = Opcion::valores($usuario->id);
        $opciones['_n'] = count($opciones);

        return View('main')->with([
            'usuario' => $usuario,
            'persona' => $persona,
            'empresas' => $empresas ?? [],
            'opciones' => $opciones,
        ]);
    }


    public function cargarTotalesGet() {
        $total_empleados = Empleado::count();
        $total_empresas = Empresa::count();

        $this->especificarRespuesta('total_empleados', $total_empleados);
        $this->especificarRespuesta('total_empresas', $total_empresas);
        return $this->retornar();
    }


    public static function limpiarBaseDeDatos() {
        Marca::where('id', '>', 0)->forceDelete();
    }


    public function importarPost() {
        $destino = Input::get('destino');

        switch ($destino) {
            case 'marca':
            case 'modelo_producto':
            case 'categoria':
            case 'tipo_producto':
                $ctrl = new ProductoController;
                return $ctrl->importarItemPost($destino);

            case 'ubicacion':
                $ctrl = new ProductoController;
                return $ctrl->importarUbicacionPost();

            case 'producto':
                $ctrl = new ProductoController;
                return $ctrl->importarProductoPost();

            case 'empleado':
                $ctrl = new EmpleadoController;
                return $ctrl->importarEmpleadosPost();

            case 'simicro_credito':
                $ctrl = new SimicroCreditoController;
                return $ctrl->importarPost();
        }

        return [
            'ok' => 0,
            'err' => 'Destino no válido',
        ];
    }

}