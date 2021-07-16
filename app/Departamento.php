<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 8:42 AM
 */

namespace App;

class Departamento extends Modelo {

    public $timestamps = true;

    protected $table = 'departamento';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_sucursal',
        'nombre',
        'tipo',
        'abreviatura',
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
            'id_usuario'    => 'integer',
            'id_sucursal'   => 'integer',
            'nombre'        => 'max:63',
            'tipo'          => 'integer',
            'abreviatura'   => 'max:5',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function sucursal() {
        return $this->hasOne('App\Sucursal', 'id', 'id_sucursal');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS

    public function getNombreAttribute() {
        return ucwords($this->attributes['nombre']);
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'departamento.id',
            'departamento.id_sucursal',
            'departamento.nombre',
            'departamento.tipo',
            'departamento.abreviatura',
            'departamento.fecha_creacion',
            'empresa.nombre AS empresa',
            'sucursal.nombre AS sucursal',
        ];

        $items = self::orderBy('departamento.nombre');

        self::verificarPermiso($items, 'departamentos', 'departamento');

        return $items
            ->join('sucursal', 'departamento.id_sucursal', '=', 'sucursal.id')
            ->join('empresa', 'sucursal.id_empresa', '=', 'empresa.id')
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}