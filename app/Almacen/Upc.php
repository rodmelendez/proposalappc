<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 22/7/2019
 * Time: 3:27 PM
 */

namespace App\Almacen;

class Upc extends \App\Modelo {

    public $timestamps = true;

    protected $table = 'almacen.upc';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_producto',
        'codigo',
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
            'id_producto'   => 'integer',
            'codigo'        => 'required|max:63',
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