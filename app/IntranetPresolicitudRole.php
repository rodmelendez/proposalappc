<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetPresolicitudRole extends Modelo
{

    protected $table = 'intranet_user_rol_presolicitud';
    protected $fillable = [
        'id_presolicitud',
        'id_usuario',
        'role'
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
            'id_presolicitud'=>'integer',
            'role'=>'integer',
            'id_usuario'=>'integer'
         
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }
    
    public static function traerData() {
        $campos = [
            'id',
            'id_presolicitud',
            'role',
            'id_usuario'
        ];

        return self::orderBy('nombre')
            ->get($campos)
            ->toArray();
    }
    public function usuario() {
        return $this->belongsTo('App\User', 'id_usuario', 'id');
    }
}
