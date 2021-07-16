<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetPresolicitud extends \App\Modelo
{
    protected $table = 'intranet_presolicitud';

    protected $fillable = [
        'id_simi',
        'id_sucursal',
        'id_cliente',
        'id_usuario',
        'tasa_interes',
        'forma_credito',
        'descripcion',
        'direccion',
        'monto_solicitado',
        'monto_asignado',
        'fecha_asignacion',
        'fecha_solicitud',
        'plazo_solicitado',
        'plazo_asignado',
        'moneda',
        'estado_etapa',
        'estado_vida',
        'estado_presolicitud',
        'id_producto'
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
            'id_cliente'=>'integer',
            'monto_solicitado' => 'required',
            'fecha_solicitud'=>'date',
            'plazo_solicitado'=>'required|integer',
            'moneda'=>'integer',
            'estado_etapa'=>'integer',

        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }

    public static function traerPresolicitudes() {

        $campos = [
            'intranet_presolicitud.id',
            'intranet_presolicitud.id_simi',
            'intranet_presolicitud.id_cliente',
            'intranet_presolicitud.tasa_interes',
            'intranet_presolicitud.forma_credito',
            'intranet_presolicitud.descripcion',
            'intranet_presolicitud.direccion',
            'intranet_presolicitud.estado_presolicitud',
            'intranet_presolicitud.id_usuario',
            'intranet_presolicitud.monto_solicitado',
            'intranet_presolicitud.fecha_asignacion',
            'intranet_presolicitud.monto_asignado',
            'intranet_presolicitud.plazo_solicitado',
            'intranet_presolicitud.fecha_solicitud',
            'intranet_presolicitud.plazo_asignado',
            'intranet_presolicitud.estado_etapa',
            'intranet_presolicitud.moneda',
            'intranet_presolicitud.estado_vida',
            'usuario.nombre as nombreCreadorCredito',
            'intranet_cliente.nombre as nombreCliente',
            'intranet_presolicitud_producto.id as producto',
            'intranet_sucursal.nombre as nombreSucursal',
            'intranet_presolicitud.id_sucursal'

        ];

        return self::orderBy('intranet_presolicitud.id','desc')
        ->leftJoin('intranet_sucursal','intranet_sucursal.id','=','intranet_presolicitud.id_sucursal')
        ->leftJoin('intranet_cliente','intranet_presolicitud.id_cliente','=','intranet_cliente.id')
        ->leftJoin('usuario','intranet_presolicitud.id_usuario','=','usuario.id')
        ->leftJoin('intranet_presolicitud_producto','intranet_presolicitud.id_producto','=','intranet_presolicitud_producto.id')
        ->get($campos);
    }

    public static function traerData() {

        $campos = [
            'intranet_presolicitud.id',
            'intranet_presolicitud.id_simi',
            'intranet_presolicitud.id_cliente',
            'intranet_presolicitud.tasa_interes',
            'intranet_presolicitud.forma_credito',
            'intranet_presolicitud.descripcion',
            'intranet_presolicitud.direccion',
            'intranet_presolicitud.estado_presolicitud',
            'intranet_presolicitud.id_usuario',
            'intranet_presolicitud.monto_solicitado',
            'intranet_presolicitud.fecha_asignacion',
            'intranet_presolicitud.monto_asignado',
            'intranet_presolicitud.plazo_solicitado',
            'intranet_presolicitud.fecha_solicitud',
            'intranet_presolicitud.plazo_asignado',
            'intranet_presolicitud.estado_etapa',
            'intranet_presolicitud.moneda',
            'intranet_presolicitud.estado_vida',
            'usuario.nombre as nombreCreadorCredito',
            'intranet_cliente.nombre as nombreCliente',
            'intranet_presolicitud_producto.id as producto',
            'intranet_sucursal.nombre as nombreSucursal',
            'intranet_presolicitud.id_sucursal'
 
        ];

        return self::orderBy('intranet_presolicitud.id','desc')
        ->leftJoin('intranet_sucursal','intranet_sucursal.id','=','intranet_presolicitud.id_sucursal')
        ->leftJoin('intranet_cliente','intranet_presolicitud.id_cliente','=','intranet_cliente.id')
        ->leftJoin('usuario','intranet_presolicitud.id_usuario','=','usuario.id')
        ->leftJoin('intranet_presolicitud_producto','intranet_presolicitud.id_producto','=','intranet_presolicitud_producto.id')
        ->get($campos)
        ->toArray();
    }

    #Relaciones
    //Una presolicitud pertenece a un cliente
    public function cliente() {
        return $this->belongsTo('App\IntranetCliente', 'id_cliente', 'id');
    }

    //Una presolicitud (cliente) pertenece a una (persona) usuario
    public function usuario() {
        return $this->belongsTo('App\User', 'id_usuario', 'id');
    }

    //Una presolicitud tiene muchas etapasDePresolicitud
    public function etapaPresolicitud() {
        return $this->hasMany('App\IntranetEtapaPresolicitud', 'id_presolicitud', 'id');
    }

    //Una presolicitud tiene muchos documentos
    public function documentoPresolicitud(){
        return $this->hasMany('App\IntranetPresolicitudDocumento', 'id_presolicitud', 'id');
    }

    //Una presolicitud tiene muchas preguntas
    public function preguntas() {
        return $this->hasMany('App\IntranetPreguntaPresolicitud', 'id_presolicitud', 'id');
    }

    //Una presolicitud tiene muchos roles
    public function presolicitudRoles() {
        return $this->hasMany('App\IntranetPresolicitudRole', 'id_presolicitud', 'id');
    }

    //Una presolicitud le pertenece a un producto
    public function producto(){
        return $this->belongsTo('App\IntranetPresolicitudProducto', 'id_producto', 'id');
    }

    //Una presolicitud le pertenece a una sucursal
    public function sucursal(){
       return $this->belongsTo('App\IntranetSucursal', 'id_sucursal', 'id');
    }

}
