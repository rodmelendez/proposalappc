<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 26/4/2019
 * Time: 9:51 PM
 */

namespace App;

class Empresa extends Modelo {

    public $timestamps = true;

    protected $table = 'empresa';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'nombre',
        'ubicacion',
        'telefono',
        'website',
        'descripcion',
        'color',
        'color_fondo',
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
            'nombre'        => 'required|max:60',
            'ubicacion'     => 'max:255',
            'telefono'      => 'max:60',
            'website'       => 'max:60',
            'descripcion'   => 'max:1000',
            'color'         => 'max:25',
            'color_fondo'   => 'max:25',
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
        return ucwords($this->attributes['nombre']);
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'nombre',
            'ubicacion',
            'telefono',
            'website',
            'descripcion',
            'color',
            'color_fondo',
            'tipo',
            'logo',
            'fecha_creacion',
        ];

        $items = self::orderBy('fecha_creacion');

        self::verificarPermiso($items, 'empresas');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}