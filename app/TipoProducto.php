<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 5:31 PM
 */

namespace App;

class TipoProducto extends Modelo {

    public $timestamps = true;

    protected $table = 'tipo';

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
            'abreviatura'   => 'required|min:1|max:4|unique:tipo,abreviatura,' . $ignorar_id . ',id,fecha_eliminacion,NULL',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function atributos() {
        return $this->belongsToMany('App\Atributo', 'tipo_atributo', 'id_tipo', 'id_atributo');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS

    public function getNombreAttribute() {
        return ucfirst($this->attributes['nombre']);
    }

    public function getAbreviaturaAttribute() {
        return strtoupper($this->attributes['abreviatura']);
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'nombre',
            'abreviatura',
            '(SELECT STRING_AGG(atributo.nombre, \', \') FROM tipo_atributo INNER JOIN atributo ON tipo_atributo.id_tipo = tipo.id AND tipo_atributo.id_atributo = atributo.id) AS lista_atributos',
            'fecha_creacion',
        ];

        $items = self::orderBy('fecha_creacion');

        self::verificarPermiso($items, 'tipos');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}