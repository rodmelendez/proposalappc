<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 5:36 PM
 */

namespace App;

class EventoProductoFoto extends Modelo {

    public $timestamps = true;

    protected $table = 'evento_foto';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_evento',
        'foto',
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
            'id_evento'     => 'integer',
            'foto'          => 'max:31',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function evento() {
        return $this->belongsTo('App\EventoProducto', 'id_evento', 'id');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'id_usuario',
            'id_evento',
            'foto',
            'fecha_creacion',
        ];
        return self::orderBy('fecha_creacion')
            ->get($campos)
            ->toArray();
    }

}