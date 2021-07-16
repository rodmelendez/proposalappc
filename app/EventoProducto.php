<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 5:33 PM
 */

namespace App;

class EventoProducto extends Modelo {

    public $timestamps = true;

    protected $table = 'evento';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_producto',
        'status',
        'observaciones',
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
            'status'        => 'integer',
            'observaciones' => 'max:511',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function fotos() {
        return $this->hasMany('App\EventoProductoFoto', 'id_evento', 'id');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS

    public function getNombreAttribute() {
        return ucfirst($this->attributes['nombre']);
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'id_producto',
            'status',
            'observaciones',
            'fecha_creacion'
        ];
        return self::orderBy('fecha_creacion')
            ->get($campos)
            ->toArray();
    }

}