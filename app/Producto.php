<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 5:14 PM
 */

namespace App;

use Illuminate\Support\Facades\DB;

class Producto extends Modelo {

    public $timestamps = true;

    protected $table = 'producto';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_tipo',
        'id_marca',
        'id_modelo',
        'nombre',
        'codigo_sistema',
        'codigo_unico',
        'foto',
        'cantidad',
        'id_categoria',
        'id_empresa',
        'id_sucursal',
        'id_departamento',
        'id_sub_departamento',
        'id_ubicacion',
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
            'id_usuario'            => 'integer',
            'id_tipo'               => 'integer',
            'id_marca'              => 'integer',
            'id_modelo'             => 'integer',
            'nombre'                => 'max:63',
            'codigo_sistema'        => 'max:31',
            'codigo_unico'          => 'max:31',
            'foto'                  => 'max:31',
            'cantidad'              => 'integer',
            'id_categoria'          => 'integer',
            'id_empresa'            => 'integer',
            'id_sucursal'           => 'integer',
            'id_departamento'       => 'integer',
            'id_sub_departamento'   => 'integer',
            'id_ubicacion'          => 'integer',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function tipo() {
        return $this->belongsTo('App\TipoProducto', 'id_tipo', 'id');
    }

    public function marca() {
        return $this->belongsTo('App\Marca', 'id_marca', 'id');
    }

    public function modelo() {
        return $this->belongsTo('App\ModeloProducto', 'id_modelo', 'id');
    }

    public function eventos() {
        return $this->hasMany('App\EventoProducto', 'id_producto', 'id');
    }

    public function atributos() {
        return $this->belongsToMany('App\Atributo', 'producto_atributo', 'id_producto', 'id_atributo');
    }

    public function categoria() {
        return $this->belongsTo('App\Categoria', 'id_categoria', 'id');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS

    public function getNombreAttribute() {
        return ucfirst($this->attributes['nombre']);
    }


    # METODOS

    public static function traerDataFiltrada($solo_no_asignados = false) {
        $campos = [
            'producto.id',
            'producto.id_tipo',
            'producto.id_marca',
            'producto.id_modelo',
            'producto.nombre',
            'producto.codigo_sistema',
            'producto.codigo_unico',
            'producto.foto',
            'producto.cantidad',
            'producto.id_empresa',
            'producto.id_sucursal',
            'producto.id_departamento',
            'producto.id_sub_departamento',
            'producto.id_ubicacion',
            'empresa.nombre AS empresa',
            'sucursal.nombre AS sucursal',
            'departamento.nombre AS departamento',
            'sub_departamento.nombre AS sub_departamento',
            'ubicacion.nombre AS ubicacion',
            'tipo.nombre AS tipo',
            'marca.nombre AS marca',
            'modelo.nombre AS modelo',
            'producto_usuario.id_usuario AS id_usuario_asignado',
            'producto_usuario.id_empleado AS id_empleado_asignado',
            '(SELECT STRING_AGG(CONCAT(pe.primer_nombre, \' \', pe.primer_apellido), \', \') FROM persona pe INNER JOIN empleado em ON em.id_persona = pe.id INNER JOIN entrega en ON en.id_empleado_colaborador = em.id INNER JOIN entrega_producto ep ON ep.id_entrega = en.id WHERE ep.id_producto = producto.id) AS nombre_asignado',
        ];

        $items = self::orderBy('producto.fecha_creacion');

        self::verificarPermiso($items, 'productos', 'producto');
        
        if (!empty($solo_no_asignados)) {
            $items = $items->whereNull('producto_usuario.id');
        }

        return $items
            ->join('empresa', 'producto.id_empresa', '=', 'empresa.id', 'left')
            ->join('sucursal', 'producto.id_sucursal', '=', 'sucursal.id', 'left')
            ->join('departamento', 'producto.id_departamento', '=', 'departamento.id', 'left')
            ->join('sub_departamento', 'producto.id_sub_departamento', '=', 'sub_departamento.id', 'left')
            ->join('ubicacion', 'producto.id_ubicacion', '=', 'ubicacion.id', 'left')
            ->join('tipo', 'producto.id_tipo', '=', 'tipo.id', 'left')
            ->join('marca', 'producto.id_marca', '=', 'marca.id', 'left')
            ->join('modelo', 'producto.id_marca', '=', 'modelo.id', 'left')
            ->join('producto_usuario', 'producto_usuario.id_producto', '=', 'producto.id', 'left')
            ->selectRaw(implode(',', $campos))
            ->get($campos)
            ->toArray();
    }
    
    
    public static function traerData($campos = null) {
        return self::traerDataFiltrada();
    }


    public function valoresAtributos() {
        return DB::table('producto_atributo')
            ->where('id_producto', '=', (int)$this->id)
            ->get(['id_atributo', 'valor'])
            ->pluck('valor', 'id_atributo')
            ->toArray();
    }


    public function listaAtributos() {
        return DB::table('producto_atributo')
            ->join('atributo', 'producto_atributo.id_atributo', '=', 'atributo.id')
            ->where('id_producto', '=', (int)$this->id)
            ->get(['id_atributo', 'nombre', 'valor'])
            ->toArray();
    }

}