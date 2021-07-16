<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 21/7/2019
 * Time: 1:50 PM
 */

namespace App\Almacen;

class Bodega extends \App\Modelo {

    public $timestamps = true;

    protected $table = 'almacen.bodega';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_empresa',
        'nombre',
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
            'id_empresa'    => 'integer',
            'nombre'        => 'required|max:63',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function divisiones() {
        return $this->hasMany('App\Almacen\Division', 'id_bodega', 'id');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'id_empresa',
            'nombre',
            'foto',
            'fecha_creacion'
        ];

        $items = self::orderBy('nombre');

        self::verificarPermiso($items, 'almacen-bodega');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}