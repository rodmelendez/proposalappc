<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 04/09/2020
 * Time: 3:19 PM
 */

namespace App\Ezadigital;

class Meta extends \App\Modelo {

    public $timestamps = true;

    protected $table = 'ezadigital.meta';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'nombre',
        'descripcion',
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
            'nombre'        => 'max:63',
            'descripcion'   => 'max:255',
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
            'nombre',
            'descripcion',
            'status',
            'fecha_creacion',
        ];

        $items = self::orderBy('nombre');

        //self::verificarPermiso($items, 'ezadigital-meta');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}