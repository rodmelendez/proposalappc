<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 21/7/2019
 * Time: 4:56 PM
 */

namespace App\Almacen;

use App\Funciones;
use Illuminate\Support\Facades\Auth;

class Producto extends \App\Modelo {

    public $timestamps = true;

    protected $table = 'almacen.producto';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'nombre',
        'referencia',
        'referencia_alternativa',
        'cantidad',
        'foto',
        'upc',
        'id_empresa',
        'id_tipo_producto',
        'id_categoria',
        'id_subcategoria',
        'id_marca',
        'id_modelo',
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
            'nombre'                    => 'required|max:63',
            'referencia'                => 'max:63',
            'referencia_alternativa'    => 'max:63',
            'cantidad'                  => 'integer',
            'upc'                       => 'max:127',
            'id_empresa'                => 'integer',
            'id_tipo_producto'          => 'integer',
            'id_categoria'              => 'integer',
            'id_subcategoria'           => 'integer',
            'id_marca'                  => 'integer',
            'id_modelo'                 => 'integer',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function empresa() {
        return $this->belongsTo('App\Empresa', 'id_empresa', 'id');
    }
    
    public function tipoProducto() {
        return $this->belongsTo('App\Almacen\TipoProducto', 'id_tipo_producto', 'id');
    }
    
    public function categoria() {
        return $this->belongsTo('App\Almacen\Categoria', 'id_categoria', 'id');
    }

    public function subcategoria() {
        return $this->belongsTo('App\Almacen\Subcategoria', 'id_subcategoria', 'id');
    }

    public function marca() {
        return $this->belongsTo('App\Almacen\Marca', 'id_marca', 'id');
    }

    public function modelo() {
        return $this->belongsTo('App\Almacen\Modelo', 'id_modelo', 'id');
    }

    public function fotos() {
        return $this->hasMany('App\Almacen\Foto', 'id_producto', 'id');
    }

    public function upcs() {
        return $this->hasMany('App\Almacen\Upc', 'id_producto', 'id');
    }

    public function celdas() {
        return $this->belongsToMany('App\Almacen\Celda', 'almacen.producto_celda', 'id_producto', 'id_celda');
    }

    public function productoCeldas() {
        return $this->hasMany('App\Almacen\ProductoCelda', 'id_producto', 'id');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'id_usuario',
            'nombre',
            'referencia',
            'referencia_alternativa',
            'id_tipo_producto',
            'id_categoria',
            'id_subcategoria',
            'id_marca',
            'id_modelo',
            'cantidad',
            'foto',
            'upc',
            'fecha_creacion',
            "(SELECT STRING_AGG(CONCAT(almacen.producto_celda.id_bodega),'|') FROM almacen.producto_celda WHERE almacen.producto_celda.id_producto = almacen.producto.id AND almacen.producto_celda.cantidad > 0 AND almacen.producto_celda.id_bodega IS NOT NULL) AS bodegas",
            "(SELECT STRING_AGG(CONCAT(almacen.producto_celda.id_division),'|') FROM almacen.producto_celda WHERE almacen.producto_celda.id_producto = almacen.producto.id AND almacen.producto_celda.cantidad > 0 AND almacen.producto_celda.id_division IS NOT NULL) AS divisiones",
            "(SELECT STRING_AGG(CONCAT(almacen.producto_celda.id_celda),'|') FROM almacen.producto_celda WHERE almacen.producto_celda.id_producto = almacen.producto.id AND almacen.producto_celda.cantidad > 0 AND almacen.producto_celda.id_celda IS NOT NULL) AS celdas",
            "(SELECT STRING_AGG(CONCAT(almacen.producto_celda.id_bodega,',',almacen.producto_celda.id_division,',',almacen.producto_celda.id_celda,':',almacen.producto_celda.cantidad),'|') FROM almacen.producto_celda WHERE almacen.producto_celda.id_producto = almacen.producto.id) AS celdas_cantidades",
        ];

        $items = self
            ::orderBy('fecha_creacion');

        self::verificarPermiso($items, 'almacen-producto');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


    public static function buscarPorReferencia($referencia, $id_ignorar = null) {
        $item = self
            ::where('referencia', '=', $referencia)
            ->orWhere('referencia_alternativa', '=', $referencia)
            ->first();

        if (!$item) return null;

        if (!empty($id_ignorar)) {
            if ($item->id == $id_ignorar) {
                return null;
            }
        }

        return $item;
    }


    public function actualizarFotos($nombres) {
        if (count($nombres)) {
            $this
                ->fotos()
                ->whereNotIn('archivo', $nombres)
                ->delete();

            $usuario = Auth::user();

            foreach ($nombres as $nombre) {
                if (empty($nombre)) continue;

                if (!$this->fotos()->where('archivo', '=', $nombre)->count()) {
                    $this->fotos()->create([
                        'archivo' => $nombre,
                        'id_usuario' => $usuario ? $usuario->id : null,
                    ]);
                }
            }
        }
        else {
            $this
                ->fotos()
                ->delete();
        }
    }


    public function actualizarUpcs($codigos) {
        if (count($codigos)) {
            $this
                ->upcs()
                ->whereNotIn('codigo', $codigos)
                ->delete();

            $usuario = Auth::user();

            foreach ($codigos as $codigo) {
                if (empty($codigo)) continue;

                if (!$this->upcs()->where('codigo', '=', $codigo)->count()) {
                    $this->upcs()->create([
                        'codigo' => $codigo,
                        'id_usuario' => $usuario ? $usuario->id : null,
                    ]);
                }
            }
        }
        else {
            $this->upcs()->delete();
        }

        $this->upc = implode(',', is_array($codigos) ? $codigos : []);
        $this->save();
    }


    public static function buscar($valor) {
        //por upc
        $item = self
            ::join('almacen.upc', 'almacen.upc.id_producto', '=', 'almacen.producto.id')
            ->where('codigo', '=', $valor)
            ->selectRaw('almacen.producto.*')
            ->first();

        if ($item) {
            return $item;
        }

        //por referencia
        $item = self
            ::where(function ($q) use ($valor) {
                $q->where('referencia', 'ILIKE', $valor);
                $q->orWhere('referencia_alternativa', 'ILIKE', $valor);
            })
            ->first();

        if ($item) {
            return $item;
        }

        return null;
    }


    public function agregar($id_celda, $cantidad = 1, $id_documento = null) {
        if (!($cantidad = (int)$cantidad)) {
            return false;
        }

        $usuario = Auth::user();

        $p_celda = $this
            ->productoCeldas()
            ->where('id_celda', '=', (int)$id_celda)
            ->first();

        if ($p_celda) {
            $p_celda->cantidad += $cantidad;
            $p_celda->save();
        }
        else {
            if (!($celda = Celda::find((int)$id_celda))) {
                return false;
            }

            $p_celda = $this->productoCeldas()->create([
                'id_usuario' => $usuario ? $usuario->id : null,
                'id_celda' => $celda->id,
                'id_division' => $celda->id_division,
                'id_bodega' => $celda->id_bodega,
                'id_empresa' => $celda->id_empresa,
                'cantidad' => $cantidad,
            ]);

            if (!$p_celda) return false;
        }

        $this->cantidad += $cantidad;
        $this->save();

        Kardex::create([
            'id_usuario' => $usuario ? $usuario->id : null,
            'id_producto' => (int)$this->id,
            'id_empresa' => $p_celda->id_empresa,
            'id_bodega' => $p_celda->id_bodega,
            'id_division' => $p_celda->id_division,
            'id_celda' => $p_celda->id_celda,
            'id_documento' => $id_documento,
            'cantidad' => (int)$cantidad,
            'fecha' => date('Y-m-d'),
        ]);

        return $p_celda;
    }


    public function quitar($id_celda, $cantidad = 1, $id_documento = null) {
        $p_celda = $this
            ->productoCeldas()
            ->where('id_celda', '=', (int)$id_celda)
            ->first();

        if (!$p_celda) {
            return false;
        }

        if ($p_celda->cantidad < $cantidad) {
            return false;
        }

        $p_celda->cantidad -= (int)$cantidad;
        $p_celda->save();

        $this->cantidad -= $cantidad;
        $this->save();

        $usuario = Auth::user();

        Kardex::create([
            'id_usuario' => $usuario ? $usuario->id : null,
            'id_producto' => (int)$this->id,
            'id_empresa' => $p_celda->id_empresa,
            'id_bodega' => $p_celda->id_bodega,
            'id_division' => $p_celda->id_division,
            'id_celda' => $p_celda->id_celda,
            'id_documento' => $id_documento,
            'cantidad' => (int)$cantidad * -1,
            'fecha' => date('Y-m-d'),
        ]);

        return $p_celda;
    }


    public function ajustar($id_celda, $cantidad) {
        $p_celda = $this
            ->productoCeldas()
            ->where('id_celda', '=', (int)$id_celda)
            ->first();

        if (!$p_celda) {
            return false;
        }

        if ($cantidad < $p_celda->cantidad) {
            return $this->quitar($id_celda, $p_celda->cantidad - $cantidad);
        }
        elseif ($cantidad > $p_celda->cantidad) {
            return $this->agregar($id_celda, $cantidad - $p_celda->cantidad);
        }

        return false;
    }


    public function listaMovimientos() {
        $movimientos = Kardex
            ::where('id_producto', '=', (int)$this->id)
            ->orderBy('fecha_creacion', 'DESC')
            ->get();

        return $movimientos;
    }

}