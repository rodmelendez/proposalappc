<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 26/4/2019
 * Time: 10:18 PM
 */

namespace App\Http\Controllers;


use App\Entrega;
use App\Departamento;
use App\Empleado;
use App\Empresa;
use App\Funciones;
use App\Producto;
use App\Subdepartamento;
use App\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class EntregaController extends Controlador {

    protected $modelo = 'Entrega';


    public function antesDeGuardar() {
        $this->antesDeGuardarDefecto();

        //valida colaborador (empleado a quien se le asigna el producto)
        if (!Input::get('id_empleado_colaborador')) {
            $this->retornarError('Debe seleccionar el empleado.');
            return false;
        }

        //valida que se envien productos
        $ids_productos = Input::get('producto_id');
        if (!is_array($ids_productos) || !count($ids_productos)) {
            $this->retornarError('Por favor seleccione el producto a entregar.');
            return false;
        }

        //busca los nombres y ids de los usuarios
        foreach (['colaborador', 'emisor', 'receptor', 'autoriza'] as $t_usuario)
        if (!$this->dataEmpleadoFromInput($t_usuario)) {
            return false;
        }

        return true;
    }


    /**
     * Después de guardar
     *
     * @param Entrega $item
     */
    public function despuesDeGuardar($item) {
        $productos = [];

        $p_ids = Input::get('producto_id');

        if (is_array($p_ids)) {
            $p_tipos = Input::get('producto_tipo');
            $p_modelo = Input::get('producto_modelo');
            $p_marca = Input::get('producto_marca');
            $p_atributos = Input::get('producto_atributos');

            foreach ($p_ids as $key => $id_producto) {
                if (empty($id_producto)) continue;

                $productos[] = [
                    'id_producto' => (int)$id_producto,
                    'tipo' => $p_tipos[$key] ?? '',
                    'modelo' => $p_modelo[$key] ?? '',
                    'marca' => $p_marca[$key] ?? '',
                    'atributos' => $p_atributos[$key] ?? '',
                ];
            }

            $item->guardarProductos($productos);
        }
    }


    /**
     * @param Entrega $item
     */
    public function itemDataAdicional($item) {
        $productos = $item->listaProductos();

        $this->especificarRespuesta('productos', $productos);
    }


    public function dataEmpleadoGet() {
        $id_empleado = (int)Input::get('id_empleado');

        if (!($empleado = Empleado::find((int)$id_empleado))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        //persona
        $persona = $empleado->persona;

        //departamento
        $departamento = $empleado->departamento;

        //sucursal
        $sucursal = $departamento ? $departamento->sucursal : null;

        //correo
        $correo = $persona ? implode(', ', $persona->correos()) : '';
        if (empty($correo) && $persona) {
            if ($usuario = $persona->usuario()->first()) {
                $correo = $usuario->nombre;
            }
        }

        $data = [
            'nombre' => $persona ? $persona->nombres() : '',
            'dni' => $persona ? $persona->dni : '',
            'correo' => $correo,
            'telefono' => $persona ? implode(', ', $persona->telefonos()) : '',
            'cargo' => $empleado->tipo_cargo,
            'departamento' => $departamento ? $departamento->nombre : '',
            'direccion' => $sucursal ? ($sucursal->ubicacion ? $sucursal->ubicacion : $sucursal->nombre) : '',
        ];

        $this->agregarArregloEnRespuesta($data);
        return $this->retornar();
    }


    public function dataProductoGet() {
        $id_producto = (int)Input::get('id_producto');

        if (!($producto = Producto::find((int)$id_producto))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        //tipo
        $tipo = $producto->tipo;

        //modelo
        $modelo = $producto->modelo;

        //marca
        $marca = $producto->marca;

        //atributos
        $atributos = $producto
            ->atributos()
            ->get(['nombre'])
            ->pluck('nombre')
            ->toArray();

        $data = [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'tipo' => $tipo ? $tipo->nombre : '',
            'modelo' => $modelo ? $modelo->nombre : '',
            'marca' => $marca ? $marca->nombre : '',
            'atributos' => $atributos,
        ];

        $this->agregarArregloEnRespuesta($data);
        return $this->retornar();
    }


    public function cargarListasGet() {
        $empleados = Empleado::traerData();
        $productos = Producto::traerData();

        $this->especificarRespuesta('empleados', $empleados);
        $this->especificarRespuesta('productos', $productos);
        return $this->retornar();
    }


    private function dataEmpleadoFromInput($key) {
        $id_empleado = Input::get('id_empleado_' . $key);

        if (empty($id_empleado)) return true;

        if (!($empleado = Empleado::find($id_empleado))) {
            $this->retornarError('Empleado ' . $key . ' no encontrado.');
            return false;
        }

        if ($persona = $empleado->persona) {
            $usuario = $persona->usuario()->first();
        }
        else {
            $usuario = null;
        }

        Input::merge([
            'nombre_' . $key => $persona ? $persona->nombres() : null,
            'id_usuario_' . $key => $usuario ? $usuario->id : null,
        ]);

        return true;
    }


    public function documentoHtmlGet() {
        $id_entrega = (int)Input::get('id_entrega');

        if (!($entrega = Entrega::find((int)$id_entrega))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        if ($empleado = $entrega->colaborador) {
            $empresa = $empleado->empresas()->first();
        }
        else {
            $empresa = null;
        }

        return View('plantillas.acta_entrega_equipo')->with([
            'logo' => $empresa ? asset('uploads/img/s/' . $empresa->logo) : '',
            'codigo' => $entrega->codigo,
            'fecha' => Funciones::formatoFechaApp($entrega->fecha),
            'num_documento' => str_pad($entrega->id, 5, '0', STR_PAD_LEFT),
            'colaborador_cargo' => $entrega->colaborador_cargo,
            'colaborador_departamento' => $entrega->colaborador_departamento,
            'colaborador_correo' => $entrega->colaborador_correo,
            'colaborador_direccion' => $entrega->colaborador_direccion,
            'colaborador_dni' => $entrega->colaborador_dni,
            'colaborador_nombre' => $entrega->colaborador_nombre,
            'colaborador_telefono' => $entrega->colaborador_telefono,
            'productos' => $entrega->listaProductos(true),
            'descripcion' => $entrega->descripcion,
            'observacion' => $entrega->observacion,
            'nombre_receptor' => $entrega->nombre_receptor,
            'nombre_emisor' => $entrega->nombre_emisor,
            'nombre_autoriza' => $entrega->nombre_autoriza,
        ]);
    }

}