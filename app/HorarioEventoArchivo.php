<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 30/8/2019
 * Time: 4:30 PM
 */

namespace App;

class HorarioEventoArchivo extends Modelo {

    public $timestamps = true;

    protected $table = 'horario_evento_archivo';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_horario_evento',
        'archivo',
        'nombre',
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
            'id_horario_evento' => 'required|integer',
            'archivo'           => 'max:255',
            'nombre'            => 'max:255',
            'tipo'              => 'integer',
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