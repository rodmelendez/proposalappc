<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 5:17 PM
 */

namespace App;

class Marca extends Modelo {

    public $timestamps = true;

    protected $table = 'marca';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'nombre',
        'abreviatura',
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
            'nombre'        => 'required|max:63',
            'abreviatura'   => 'required|min:1|max:4|unique:marca,abreviatura,' . $ignorar_id . ',id,fecha_eliminacion,NULL',
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

    public function getAbreviaturaAttribute() {
        return strtoupper($this->attributes['abreviatura']);
    }


    # METODOS

    public static function traerData() {
        $campos = [
            'id',
            'nombre',
            'abreviatura',
            'fecha_creacion'
        ];

        $items = self::orderBy('nombre');

        self::verificarPermiso($items, 'marcas');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}