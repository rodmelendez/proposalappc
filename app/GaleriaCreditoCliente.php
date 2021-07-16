<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 13/5/2019
 * Time: 3:22 PM
 */

namespace App;

class GaleriaCreditoCliente extends Modelo {

    public $timestamps = true;

    protected $table = 'galeria_credito_cliente';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'nombre',
        'dni',
        'tipo',
        'negocio',
        'ruc',
        'direccion',
        'telefono',
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
            'dni'           => 'max:31',
            'tipo'          => 'integer',
            'negocio'       => 'max:63',
            'ruc'           => 'max:31',
            'direccion'     => 'max:127',
            'telefono'      => 'max:127',
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
        return ucfirst($this->attributes['nombre']);
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'nombre',
            'dni',
            'tipo',
            'negocio',
            'ruc',
            'direccion',
            'telefono',
            'fecha_creacion'
        ];
        return self::orderBy('fecha_creacion')
            ->get($campos)
            ->toArray();
    }

}