<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 5:46 PM
 */

namespace App;

class Atributo extends Modelo {

    public $timestamps = true;

    protected $table = 'atributo';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
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
            'id_usuario'    => 'integer',
            'nombre'        => 'required|max:63',
            'tipo'          => 'integer',
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
            'tipo',
            'fecha_creacion',
        ];

        $items = self::orderBy('nombre');

        self::verificarPermiso($items, 'atributos');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}