<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetDocumento extends \App\Modelo
{
    protected $table = 'intranet_documento';
    protected $fillable = [     
        'nombre',
        'descripcion',
        'tipo',
        'opcional',
        'proceso',
        'id_usuario',
        'id_documento_categoria'

    ];
    protected $hidden = ['created_at','updated_at'];
    
     /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto
     *
     * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
     * @return array|string
     */
    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'nombre'=>'max:255',
            'descripcion'=>'max:255',
            'tipo'=>'integer',
            'proceso'=>'integer',
            'id_usuario'=>'integer',
            'id_documento_categoria'=>'integer'
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }
    public static function traerData() {
        $campos = [
            'id',
            'nombre',
            'descripcion',
            'tipo',
            'opcional',
            'proceso',
            'id_usuario',
            'id_documento_categoria'
        ];
        self::orderBy('nombre');

        return self::orderBy('nombre')
            ->get($campos)
            ->toArray();
    }

    public function documentoEnPresolicitud() {
        return $this->hasMany('App\IntranetPresolicitudDocumento', 'id_documento', 'id');
    }
}
