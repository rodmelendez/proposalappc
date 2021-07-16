<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 24/7/2019
 * Time: 8:39 PM
 */

namespace App\Almacen;

class ProductoCelda extends \App\Modelo {

    public $timestamps = true;

    protected $table = 'almacen.producto_celda';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_producto',
        'id_celda',
        'id_division',
        'id_bodega',
        'id_empresa',
        'cantidad',
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
            'id_producto'   => 'integer',
            'id_celda'      => 'integer',
            'id_division'   => 'integer',
            'id_bodega'     => 'integer',
            'id_empresa'    => 'integer',
            'cantidad'      => 'integer',
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


    # METODOS

}