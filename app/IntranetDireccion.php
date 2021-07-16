<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetDireccion extends \App\Modelo
{
    protected $table = 'intranet_direccion';
    protected $fillable = [     
       
        'id_cliente',
        'id_barrio',
        'descripcion',
        'pertenece',
        'coordenadas',
        'tipo_direccion',

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
            'id_cliente'=> 'integer',
            'id_barrio'=> 'integer',
            'descripcion'=>'max:63',
            'pertenece'=>'max:63',
            'coordenadas'=>'max:63',
            'tipo_direccion'=>'max:63',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }
    public static function traerData() {
        $campos = [
            'id_cliente',
            'id_barrio',
            'descripcion',
            'pertenece',
            'coordenadas',
            'tipo_direccion'
        ];
        self::orderBy('id_cliente');

        return self::orderBy('id_cliente')
            ->get($campos)
            ->toArray();
    }

    // una direccion pertenece a un cliente
    public function cliente() {
        return $this->belongsTo('App\IntranetCliente', 'id_cliente', 'id');
    }
    //un guetto pertenece a un cliente
    public function barrio() {
        return $this->belongsTo('App\IntranetCliente', 'id_barrio', 'id');
    }
}
