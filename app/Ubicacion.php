<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 6/5/2019
 * Time: 5:10 PM
 */

namespace App;

class Ubicacion extends Modelo {

    public $timestamps = true;

    protected $table = 'ubicacion';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_sub_departamento',
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
            'id_usuario'            => 'integer',
            'id_sub_departamento'   => 'integer',
            'id_sucursal'           => 'integer',
            'nombre'                => 'max:63',
            'tipo'                  => 'integer',
            'abreviatura'           => 'max:5',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES


    # FILTROS


    # ASIGNACIONES


    # LECTURAS

    public function getNombreAttribute() {
        return ucwords($this->attributes['nombre']);
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'ubicacion.id',
            'ubicacion.id_sub_departamento',
            'ubicacion.id_sucursal',
            'ubicacion.nombre',
            'ubicacion.tipo',
            'ubicacion.abreviatura',
            'ubicacion.fecha_creacion',
            'empresa.nombre AS empresa',
            'sucursal.nombre AS sucursal',
            'departamento.nombre AS departamento',
            'sub_departamento.nombre AS sub_departamento',
        ];

        $items = self::orderBy('ubicacion.nombre');

        self::verificarPermiso($items, 'ubicaciones', 'ubicacion');

        return $items
            ->join('sub_departamento', 'ubicacion.id_sub_departamento', '=', 'sub_departamento.id')
            ->join('departamento', 'sub_departamento.id_departamento', '=', 'departamento.id')
            ->join('sucursal', 'departamento.id_sucursal', '=', 'sucursal.id')
            ->join('empresa', 'sucursal.id_empresa', '=', 'empresa.id')
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}