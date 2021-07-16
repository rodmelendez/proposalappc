<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 6/5/2019
 * Time: 4:32 PM
 */

namespace App;

class Subdepartamento extends Modelo {

    public $timestamps = true;

    protected $table = 'sub_departamento';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_departamento',
        'nombre',
        'abreviatura',
        'tipo',
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
            'id_usuario'        => 'integer',
            'id_departamento'   => 'integer',
            'nombre'            => 'max:63',
            'tipo'              => 'integer',
            'abreviatura'       => 'max:5',
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
            'sub_departamento.id',
            'sub_departamento.id_departamento',
            'sub_departamento.nombre',
            'sub_departamento.tipo',
            'sub_departamento.abreviatura',
            'sub_departamento.fecha_creacion',
            'empresa.nombre AS empresa',
            'sucursal.nombre AS sucursal',
            'departamento.nombre AS departamento',
        ];

        $items = self::orderBy('sub_departamento.nombre');

        self::verificarPermiso($items, 'subdepartamentos', 'sub_departamento');

        return $items
            ->join('departamento', 'sub_departamento.id_departamento', '=', 'departamento.id')
            ->join('sucursal', 'departamento.id_sucursal', '=', 'sucursal.id')
            ->join('empresa', 'sucursal.id_empresa', '=', 'empresa.id')
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}