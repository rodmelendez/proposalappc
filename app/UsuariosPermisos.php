<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsuariosPermisos extends Modelo
{
    protected $table = 'usuario_permiso';
    protected $fillable = [
        'id_permiso',
        'id_usuario',
       
    ];


    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'id_usuario'            => 'integer',
            'id_sub_departamento'   => 'integer',
            'id_sucursal'           => 'integer',
            'nombre'                => 'max:63',
            'tipo'                  => 'integer',
            'abreviatura'           => 'max:5',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }

    # METODOS
    public function usuario() {
        return $this->belongsTo('App\User', 'id_usuario', 'id');
    }
    public static function traerData($campos = null) {
        $campos = [
            'id',
            'id_permiso',
        'id_usuario',
        ];

      

        return self::orderBy('id')
        ->get($campos)
        ->toArray();
    }
}
