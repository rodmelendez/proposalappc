<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use  App\IntranetContacto;
use  App\IntranetDireccion;

class IntranetCliente extends \App\Modelo
{
    protected $table = 'intranet_cliente';
    protected $fillable = [
        'nombre',
        'id_sucursal',
        'id_simi',
        'id_tipo',
        'fecha_registro',
        'nombre_simi',
        'id_usuario',
        'ruc',
        'dni',
        'estado',
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
            'id_sucursal'=> 'integer|nullable',
            'id_simi' => 'integer|nullable',
            //'fecha_registro' =>'required|string',
            'id_tipo' => 'integer',
            'nombre'=>'max:63|required',
            'nombre_simi'=> 'max:63|nullable',
            'id_usuario' =>'integer',
            'estado' => 'max:30',
            'ruc'=>'max:60|nullable',
            'dni'=>'max:60|required',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }
    
    public static function traerData() {
        $campos = [
            'id',
            'id_sucursal',
            'id_simi' ,
            'id_tipo' ,
            'fecha_registro' ,
            'nombre_simi',
            'nombre',
            'id_usuario' ,
            'estado'
        ];

        return  self::orderBy('nombre')->get($campos)->toArray();
    }

    // Detalle de un cliente
    /*public static function detalleDeCliente($id){
        $data = array();
        $data->contactos = $this->contacto()->get();
        $data->direcciones = $this->direccion()->get();
        $data->cliente = IntranetCliente::findOrFail($id);
        return $data;
    }*/


    // un cliente pertence a una persona
    public function usuarioCliente() {
        return $this->belongsTo('App\User', 'id_usuario', 'id');
    }
    // un cliente pertence a una persona
    public function personas() {
        return $this->hasMany('App\IntranetPersona', 'id_cliente', 'id');
    }
    // un cliente pertenece a una empresa    
    public function empresas() {
        return $this->hasMany('App\IntranetEmpresa', 'id_cliente', 'id');
    }
    //un cliente tiene muchos contactos
     public function contacto()
    {
        return $this->hasMany('App\IntranetContacto', 'id_cliente', 'id');
    }
    //un cliente tiene muchas direcciones
    public function direccion()
    {
        return $this->hasMany('App\IntranetDireccion', 'id_cliente', 'id');
    }
    //un cliente tiene muchas presolicitudes
    public function presolicitudes(){
        return $this->hasMany('App\IntranetPresolicitud','id_cliente','id');
    }
    //un cliente tiene muchos eventos
    public function eventos(){
        return $this->hasMany('App\IntranetEventos', 'id_cliente', 'id');
    }
    
}
