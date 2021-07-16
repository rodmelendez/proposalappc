<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 04/09/2020
 * Time: 3:19 PM
 */

namespace App\Ezadigital;

use App\Funciones;

class MetaUsuario extends \App\Modelo {

    public $timestamps = true;

    protected $table = 'ezadigital.meta_usuario';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_meta',
        'id_usuario',
        'valor',
        'fecha',
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
            'id_meta'       => 'required|integer',
            'id_usuario'    => 'required|integer',
            'valor'         => 'max:31',
            'fecha'         => 'nullable|date_format:d/m/Y',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES


    # FILTROS


    # ASIGNACIONES

    public function setFechaAttribute($val) {
        if (!empty($val)) {
            $this->attributes['fecha'] = Funciones::formatoFechaSistema($val);
        }
    }


    # LECTURAS


    # METODOS

    public static function dataParaUsuario($id_usuario = null) {
        $campos = [
            'meta_usuario.id',
            'meta_usuario.id_meta',
            'meta_usuario.id_usuario',
            'meta.nombre AS meta',
            'meta_usuario.valor',
            'meta_usuario.fecha',
            'meta_usuario.status',
            'meta_usuario.fecha_creacion',
        ];

        $items = self
            ::join('ezadigital.meta', 'meta_usuario.id_meta', '=', 'meta.id')
            ->orderBy('fecha', 'DESC');

        if (!empty($id_usuario)) {
            $items->where('meta_usuario.id_usuario', '=', (int)$id_usuario);
        }

        //self::verificarPermiso($items, 'ezadigital-meta');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


    public static function traerData($campos = null) {
        return self::dataParaUsuario();
    }

}