<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 30/4/2019
 * Time: 3:42 PM
 */

namespace App;

class Inventario extends Modelo {

    public $timestamps = true;

    protected $table = 'inventario';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'nombre',
        'tipo',
        'marca',
        'modelo',
        'serie',
        'color',
        'estado',
        'responsable',
        'ubicacion',
        'cantidad',
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
            'nombre'        => 'max:63',
            'tipo'          => 'max:63',
            'marca'         => 'max:63',
            'modelo'        => 'max:63',
            'serie'         => 'max:63',
            'color'         => 'max:63',
            'estado'        => 'max:63',
            'responsable'   => 'max:63',
            'ubicacion'     => 'max:63',
            'cantidad'      => 'integer',
            'foto'          => '',
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

    public function getNombreAttribute() {
        return ucwords($this->attributes['nombre']);
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'nombre',
            'tipo',
            'marca',
            'modelo',
            'serie',
            'color',
            'estado',
            'responsable',
            'ubicacion',
            'cantidad',
            'foto',
            'fecha_creacion',
        ];
        return self::orderBy('fecha_creacion', 'DESC')
            ->get($campos)
            ->toArray();
    }

}