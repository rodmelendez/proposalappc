<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 2019-09-20
 * Time: 8:25 AM
 */

namespace App;

class CredencialCategoria extends Modelo {

    public $timestamps = true;

    protected $table = 'credencial_categoria';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
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
            'nombre'        => 'required|max:63',
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


    # METODOS

    public static function traerData() {
        $campos = [
            'id',
            'nombre',
            'fecha_creacion',
        ];

        $items = self::orderBy('nombre', 'DESC');

        //self::verificarPermiso($items, 'credenciales-categoria');

        return $items->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}