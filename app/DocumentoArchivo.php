<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 16/6/2019
 * Time: 6:16 PM
 */

namespace App;

class DocumentoArchivo extends Modelo {

    public $timestamps = true;

    protected $table = 'documento_archivo';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_documento',
        'archivo',
        'autor',
        'fecha',
        'bytes',
        'num_paginas',
        'url',
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
            'id_documento'  => 'integer',
            'archivo'       => 'max:63',
            'autor'         => 'max:63',
            'fecha'         => '',
            'bytes'         => 'integer',
            'num_paginas'   => 'integer',
            'url'           => 'max:2047',
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

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'id_usuario',
            'id_documento',
            'archivo',
            'autor',
            'fecha',
            'bytes',
            'num_paginas',
            'url',
            'fecha_creacion'
        ];
        return self::orderBy('fecha_creacion')
            ->get($campos)
            ->toArray();
    }

}