<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 17/7/2019
 * Time: 5:54 PM
 */

namespace App;

class HorarioEvento extends Modelo {

    public $timestamps = true;

    protected $table = 'horario_evento';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_horario',
        'dia',
        'horas',
        'tipo',
        'porcentaje',
        'comentario',
        'nota',
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
            'id_horario'    => 'required|integer',
            'dia'           => 'integer',
            'horas'         => 'integer',
            'tipo'          => 'integer',
            'porcentaje'    => 'numeric',
            'comentario'    => 'max:255',
            'nota'          => '',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function archivos() {
        return $this->hasMany('App\HorarioEventoArchivo', 'id_horario_evento', 'id');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

}