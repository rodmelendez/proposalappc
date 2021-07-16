<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 23/7/2019
 * Time: 10:11 PM
 */

namespace App\Almacen;

class DocumentoProducto extends \App\Modelo {

    public $timestamps = true;

    protected $table = 'almacen.documento_producto';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_documento',
        'id_producto',
        'id_bodega',
        'id_division',
        'id_celda',
        'cantidad',
    ];


    const STATUS_PENDIENTE = 1;
    const STATUS_PROCESADO = 2;


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
            'id_documento'  => 'integer',
            'id_producto'   => 'integer',
            'id_bodega'     => 'integer',
            'id_division'   => 'integer',
            'id_celda'      => 'integer',
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