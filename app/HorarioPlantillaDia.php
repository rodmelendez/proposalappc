<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 16/7/2019
 * Time: 12:16 PM
 */

namespace App;

class HorarioPlantillaDia extends Modelo {

    public $timestamps = true;

    protected $table = 'horario_plantilla_dia';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_horario_plantilla',
        'dia',
        'hora_inicio',
        'hora_fin',
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
            'id_horario_plantilla'  => 'required|integer',
            'dia'                   => 'integer',
            'hora_inicio'           => '',
            'hora_fin'              => '',
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