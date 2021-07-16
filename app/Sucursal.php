<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 8:53 AM
 */

namespace App;

class Sucursal extends Modelo {

    public $timestamps = true;

    protected $table = 'sucursal';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_empresa',
        'nombre',
        'ubicacion',
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
            'id_empresa'    => 'integer',
            'nombre'        => 'max:60',
            'ubicacion'     => 'max:255',
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
            'sucursal.id',
            'sucursal.id_empresa',
            'empresa.nombre AS empresa',
            'sucursal.nombre',
            'sucursal.ubicacion',
            'sucursal.fecha_creacion',
        ];

        $items = self::orderBy('sucursal.nombre');

        self::verificarPermiso($items, 'sucursales', 'sucursal');

        return $items
            ->join('empresa', 'sucursal.id_empresa', '=', 'empresa.id')
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}