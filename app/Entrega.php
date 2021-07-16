<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 08/01/2020
 * Time: 6:58 PM
 */

namespace App;

use Illuminate\Support\Facades\DB;

class Entrega extends Modelo {

    public $timestamps = true;

    protected $table = 'entrega';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'fecha',
        'fecha_recibido',
        'codigo',
        'num_documento',
        'version',
        'descripcion',
        'observacion',
        'colaborador_cargo',
        'colaborador_departamento',
        'colaborador_correo',
        'colaborador_direccion',
        'colaborador_dni',
        'colaborador_nombre',
        'colaborador_telefono',
        'nombre_receptor',
        'nombre_emisor',
        'nombre_autoriza',
        'id_usuario_colaborador',
        'id_usuario_emisor',
        'id_usuario_receptor',
        'id_usuario_autoriza',
        'id_empleado_colaborador',
        'id_empleado_emisor',
        'id_empleado_receptor',
        'id_empleado_autoriza',
    ];


    /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto
     *
     * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
     * @return array|string
     */
    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'id_usuario'                => 'integer',
            'fecha'                     => '',
            'fecha_recibido'            => '',
            'codigo'                    => 'max:63',
            'num_documento'             => 'max:63',
            'version'                   => 'max:63',
            'descripcion'               => '',
            'observacion'               => '',
            'colaborador_cargo'         => 'max:127',
            'colaborador_departamento'  => 'max:127',
            'colaborador_correo'        => 'max:63',
            'colaborador_direccion'     => 'max:255',
            'colaborador_dni'           => 'max:31',
            'colaborador_nombre'        => 'max:255',
            'colaborador_telefono'      => 'max:127',
            'nombre_receptor'             => 'max:255',
            'nombre_emisor'            => 'max:255',
            'nombre_autoriza'           => 'max:255',
            'id_usuario_colaborador'    => 'integer',
            'id_usuario_emisor'         => 'integer',
            'id_usuario_receptor'       => 'integer',
            'id_usuario_autoriza'       => 'integer',
            'id_empleado_colaborador'   => 'integer',
            'id_empleado_emisor'        => 'integer',
            'id_empleado_receptor'      => 'integer',
            'id_empleado_autoriza'      => 'integer',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function productos() {
        return $this->belongsToMany('App\Producto', 'entrega_producto', 'id_entrega', 'id_producto');
    }

    public function colaborador() {
        return $this->belongsTo('App\Empleado', 'id_empleado_colaborador', 'id');
    }


    # FILTROS


    # ASIGNACIONES

    public function setFechaAttribute($val) {
        if (!empty($val)) {
            $this->attributes['fecha'] = Funciones::formatoFechaSistema($val);
        }
    }


    # LECTURAS


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'fecha',
            'fecha_recibido',
            'codigo',
            'num_documento',
            'version',
            'descripcion',
            'observacion',
            'colaborador_cargo',
            'colaborador_departamento',
            'colaborador_correo',
            'colaborador_direccion',
            'colaborador_dni',
            'colaborador_nombre',
            'colaborador_telefono',
            'nombre_receptor',
            'nombre_emisor',
            'nombre_autoriza',
            'id_usuario_colaborador',
            'id_usuario_emisor',
            'id_usuario_receptor',
            'id_usuario_autoriza',
            'id_usuario',
            'fecha_creacion',
            '(SELECT STRING_AGG(p.nombre, \', \') FROM entrega_producto ep INNER JOIN producto p ON ep.id_producto = p.id AND ep.id_entrega = entrega.id) AS productos',
        ];

        $items = self::orderBy('fecha_creacion');

        self::verificarPermiso($items, 'empresas', 'entrega');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


    public function guardarProductos($productos) {
        $productos_asignados = $this
            ->productos()
            ->get(['producto.id'])
            ->pluck('id')
            ->toArray();

        $e_productos = [];

        if (is_array($productos) && count($productos)) {
            $usuario = User::find((int)$this->id_usuario_colaborador);
            $empleado = $usuario ? Empleado::deUsuario($usuario) : null;
            $fecha = date('Y-m-d H:i:s');

            foreach ($productos as $producto) {
                $e_productos[$producto['id_producto']] = $producto;

                if (!$usuario) continue;
                if ($indice = array_search($producto['id_producto'], $productos_asignados)) {

                    //el producto estaba asignado en esta misma entrega (posiblemente editando el documento)
                    ProductoUsuario
                        ::where('id_usuario', '=', (int)$usuario->id)
                        ->where('id_producto', (int)$producto['id_producto'])
                        ->update([
                            'id_usuario' => $usuario->id,
                            'id_empleado' => $empleado ? $empleado->id : null,
                            'id_persona' => $empleado ? $empleado->id_persona : null,
                            'fecha_creacion' => $fecha,
                        ]);

                    unset($productos_asignados[$indice]);
                }
                else {
                    $producto_usuario = ProductoUsuario
                        ::where('id_producto', '=', (int)$producto['id_producto'])
                        ->first();

                    //el producto ya esta asignado a otro usuario, se hace un reemplazo
                    if ($producto_usuario) {
                        $producto_usuario->id_usuario = $usuario->id;
                        $producto_usuario->id_empleado = $empleado ? $empleado->id : null;
                        $producto_usuario->id_persona = $empleado ? $empleado->id_persona : null;
                        $producto_usuario->fecha_creacion = $fecha;
                        $producto_usuario->save();
                    }
                    //el producto no ha sido asignado previamente, se crea el registro
                    else {
                        ProductoUsuario::create([
                            'id_producto' => (int)$producto['id_producto'],
                            'id_usuario' => $usuario->id,
                            'id_empleado' => $empleado ? $empleado->id : null,
                            'id_persona' => $empleado ? $empleado->id_persona : null,
                            'fecha_creacion' => $fecha,
                        ]);
                    }
                }

            }

            $this->productos()->sync($productos);
        }
        else {
            $this->productos()->sync([]);
        }

        //si habian productos asignados y no fueron reasignados, se eliminan
        if (count($productos_asignados)) {
            ProductoUsuario
                ::whereIn('id_producto', $productos_asignados)
                ->delete();
        }
    }


    public function listaProductos($valores_atributos = false) {
        $productos = [];

        $items = EntregaProducto
            ::where('id_entrega', '=', (int)$this->id)
            ->get();

        foreach ($items as $item) {
            if ($item->atributos) {
                if (!$valores_atributos) {
                    $atributos = explode('|', $item->atributos);
                }
                else {
                    $atributos = DB::table('producto_atributo')
                        ->where('id_producto', '=', (int)$item->id_producto)
                        ->join('atributo', 'producto_atributo.id_atributo', '=', 'atributo.id')
                        ->get(['atributo.nombre', 'producto_atributo.valor'])
                        ->pluck('valor', 'nombre')
                        ->toArray();
                }
            } else {
                $atributos = [];
            }
            $productos[] = [
                'id_producto' => $item->id_producto,
                'tipo' => $item->tipo,
                'modelo' => $item->modelo,
                'marca' => $item->marca,
                'atributos' => $atributos,
            ];
        }

        return $productos;
    }

}