<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 16/6/2019
 * Time: 5:27 PM
 */

namespace App;

class Documento extends Modelo {

    public $timestamps = true;

    protected $table = 'documento';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_documento_categoria',
        'tipo',
        'nombre',
        'descripcion',
        'archivo',
        'id_empresa',
        'id_sucursal',
        'id_departamento',
        'id_sub_departamento',
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
            'id_usuario'                => 'integer',
            'id_documento_categoria'    => 'integer',
            'tipo'                      => 'integer',
            'nombre'                    => 'max:63',
            'descripcion'               => 'max:255',
            'archivo'                   => 'max:255',
            'id_empresa'                => 'integer',
            'id_sucursal'               => 'integer',
            'id_departamento'           => 'integer',
            'id_sub_departamento'       => 'integer',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function archivos() {
        return $this->hasMany('App\DocumentoArchivo', 'id_documento', 'id');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'id_usuario',
            'id_documento_categoria',
            'tipo',
            'nombre',
            'archivo',
            'descripcion',
            'id_empresa',
            'id_sucursal',
            'id_departamento',
            'id_sub_departamento',
            'fecha_creacion'
        ];
        return self::orderBy('fecha_creacion')
            ->get($campos)
            ->toArray();
    }

}