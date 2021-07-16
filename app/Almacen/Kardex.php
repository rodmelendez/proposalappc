<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 24/7/2019
 * Time: 8:06 PM
 */

namespace App\Almacen;

class Kardex extends \App\Modelo {

    public $timestamps = true;

    protected $table = 'almacen.kardex';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_producto',
        'id_empresa',
        'id_bodega',
        'id_division',
        'id_celda',
        'id_documento',
        'cantidad',
        'fecha',
    ];


    const FUENTE_SALIDA = 0;
    const FUENTE_ENTRADA = 1;


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
            'id_empresa'    => 'integer',
            'id_bodega'     => 'integer',
            'id_division'   => 'integer',
            'id_celda'      => 'integer',
            'id_documento'  => 'integer',
            'cantidad'      => 'integer',
            'fecha'         => '',
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