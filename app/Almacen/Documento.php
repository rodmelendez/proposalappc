<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 21/7/2019
 * Time: 3:33 PM
 */

namespace App\Almacen;

use App\Funciones;
use Illuminate\Support\Facades\Auth;

class Documento extends \App\Modelo {

    public $timestamps = true;

    protected $table = 'almacen.documento';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_empresa',
        'tipo',
        'referencia',
        'numero',
        'comentario',
        'fecha',
    ];

    const STATUS_PENDIENTE = 1;
    const STATUS_PROCESADO = 2;

    const TIPO_SALIDA = 0;
    const TIPO_ENTRADA = 1;
    const TIPO_AJUSTE = 2;


    /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto
     *
     * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
     * @return array|string
     */
    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'id_usuario'    => 'integer',
            'id_empresa'    => 'integer',
            'tipo'          => 'integer',
            'referencia'    => 'max:255',
            'numero'        => 'integer',
            'comentario'    => '',
            'fecha'         => 'date_format:d/m/Y',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function productos() {
        return $this->belongsToMany('App\Almacen\Producto', 'almacen.producto_celda', 'id_documento', 'id_producto');
    }

    public function documentoProducto() {
        return $this->hasMany('App\Almacen\DocumentoProducto', 'id_documento', 'id');
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
        return [];
    }


    public static function traerDataTipo($tipo) {
        $campos = [
            'id',
            'id_empresa',
            'tipo',
            'referencia',
            'numero',
            'comentario',
            'fecha',
            'fecha_creacion',
            'status',
            '(SELECT COUNT(id) FROM almacen.documento_producto WHERE id_documento = almacen.documento.id) AS total_productos'
        ];

        $items = self
            ::where('tipo', '=', (int)$tipo)
            ->orderBy('fecha', 'DESC')
            ->orderBy('fecha_creacion', 'DESC');

        self::verificarPermiso($items, 'almacen-documento');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


    public function guardarProductos($id_productos, $cantidades, $finalizar = false) {
        if (!is_array($id_productos)) return;

        $id_guardados = [];

        $usuario = Auth::user();

        foreach ($id_productos as $id_producto) {
            if (isset($cantidades[$id_producto])) {
                foreach ($cantidades[$id_producto] as $id_celda => $cantidad) {
                    $item = $this->documentoProducto()
                        ->where('id_producto', '=', (int)$id_producto)
                        ->where('id_celda', '=', (int)$id_celda)
                        ->first();

                    if ($item) {
                        $item->cantidad = $cantidad;
                        $item->save();

                        $id_guardados[] = $item->id;
                    }
                    else {
                        if (!($celda = Celda::find((int)$id_celda))) {
                            continue;
                        }

                        $item = $this->documentoProducto()->create([
                            'id_usuario' => $usuario ? $usuario->id : null,
                            'id_producto' => $id_producto,
                            'id_bodega' => $celda->id_bodega,
                            'id_division' => $celda->id_division,
                            'id_celda' => $celda->id,
                            'cantidad' => $cantidad,
                        ]);

                        if ($item) {
                            $id_guardados[] = $item->id;
                        }
                    }
                }
            }
            else {
                $item = $this->documentoProducto()
                    ->where('id_producto', '=', (int)$id_producto)
                    ->whereNull('id_celda')
                    ->first();

                if ($item) {
                    $item->cantidad = 0;
                    $item->save();

                    $id_guardados[] = $item->id;
                }
                else {
                    $item = $this->documentoProducto()->create([
                        'id_usuario' => $usuario ? $usuario->id : null,
                        'id_producto' => $id_producto,
                        'id_bodega' => null,
                        'id_division' => null,
                        'id_celda' => null,
                        'cantidad' => 0,
                    ]);

                    if ($item) {
                        $id_guardados[] = $item->id;
                    }
                }
            }
        }

        if (!count($id_guardados)) {
            $this->documentoProducto()->delete();
        }
        else {
            $this->documentoProducto()->whereNotIn('id', $id_guardados)->delete();
        }

        if ($finalizar) {
            $this->finalizar();
        }
    }


    //proceso para afectar el inventario
    public function finalizar() {
        $d_productos = $this->documentoProducto()->get();

        foreach ($d_productos as $d_producto) {
            if (!($producto = Producto::find($d_producto->id_producto))) {
                continue;
            }

            if ($this->tipo == self::TIPO_ENTRADA) {
                $producto->agregar($d_producto->id_celda, $d_producto->cantidad, $this->id);
            }
            else {
                $producto->quitar($d_producto->id_celda, $d_producto->cantidad, $this->id);
            }

            $d_producto->status = DocumentoProducto::STATUS_PROCESADO;
            $d_producto->save();
        }

        $usuario = Auth::user();

        $this->id_usuario_aprobacion = $usuario ? $usuario->id : null;
        $this->fecha_aprobacion = date('Y-m-d');
        $this->status = self::STATUS_PROCESADO;
        $this->save();
    }


    public static function siguiente($tipo) {
        return intval(self::where('tipo', '=', (int)$tipo)->max('numero')) + 1;
    }


    public function items() {
        $campos = [
            'almacen.documento_producto.id',
            'almacen.documento_producto.id_producto',
            'almacen.documento_producto.id_bodega',
            'almacen.documento_producto.id_division',
            'almacen.documento_producto.id_celda',
            'almacen.documento_producto.cantidad',
            'almacen.producto.nombre',
            'almacen.producto.referencia',
            'almacen.producto.referencia_alternativa',
        ];

        $items = $this->documentoProducto()
            ->join('almacen.producto', 'almacen.documento_producto.id_producto', '=', 'almacen.producto.id')
            ->selectRaw(implode(',', $campos))
            ->get();

        return $items;
    }

}