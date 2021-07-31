<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\IntranetDireccion;
use App\IntranetCliente;
use App\IntranetContacto;
use App\IntranetBarrio;
use App\IntranetEmpresa;

class IntranetClienteController extends Controlador
{

    protected $modelo = 'IntranetCliente';

    public function cargarListadosGet(){
       $barrios = IntranetBarrio::all();
       $item = 'barrios';
       $this->especificarRespuesta($item,$barrios);
       return $this->retornar();
    }

    public function registrarClientePost(){
        //recibe todos los datos, menos los datos de direccion y contacto
        $cliente = input::except('id','_fuente','_accion','direcciones','contactos');
        $validarCliente = new IntranetCliente;
        //recibe todos los datos menos los datos de  contacto y cliente "direcciones"
        $direcciones = input::except('_fuente','_accion','id_tipo','fecha_registro','nombre','contactos');
        $direcciones = json_decode($direcciones['direcciones'],true);
        $validarDirecciones = new IntranetDireccion;
        //recibe todos los datos, menos los doreccion y contacto
        $contactos = input::except('_fuente','_accion','id_tipo','fecha_registro','nombre','direcciones');
        $contactos = json_decode($contactos['contactos'],true);
        $validarContactos = new IntranetContacto;
        //valida que los datos esten en orden
        $validacionCliente = Validator::make(
            $cliente,
             $validarCliente->reglasValidacion(null, (int)Input::get('id'))
         );
        
         if($validacionCliente->passes()){
            $direccionesAlmacenadas = array();
            $contactosAlmacenados = array();
            foreach ($direcciones as $direccion) {
                # code...
                $arrayDireccion=[ 
                    'id_cliente'=> 0,
                    'id_barrio'=> (integer) $direccion['id_barrio'],
                    'descripcion'=> $direccion['descripcion'],
                    'pertenece'=> $direccion['pertenece']['nombre'],
                    'tipo_direccion'=> $direccion['tipo_direccion']['nombre'],
                ];
                $validacionDireccion = Validator::make(
                    $arrayDireccion,
                    $validarDirecciones->reglasValidacion(null, (int)Input::get('id'))
                );
            
                if(!$validacionDireccion->passes() ){
                    $this->retornarError('dirrecion','existe una direccion con datos no permitidos');
                }
            }
            foreach ($contactos as $contacto) {
              
                $arrayContacto = [ 
                    'id_cliente'=> 0,
                    'tipo'=> $contacto['tipo']['nombre'],
                    'pertenece'=> $contacto['pertenece']['nombre'],
                    'descripcion'=> $contacto['descripcion'],
                    'observacion'=> $contacto['observacion'],
                ];
                $validacionContacto = Validator::make(
                    $arrayContacto,
                     $validarContactos->reglasValidacion(null, (int)Input::get('id'))
                 );
                 if(!$validacionContacto->passes() ){
                    $this->retornarError('contacto','existe un contacto con datos no permitidos');
                }
            }
            
            $item = 'cliente creado';
            $nuevo = IntranetCliente::create($cliente);
            
            foreach ($direcciones as $direccion) {
                # code...
                $arrayDireccion=[ 
                    'id_cliente'=>  (integer) $nuevo->id,
                    'id_barrio'=> (integer) $direccion['id_barrio'],
                    'descripcion'=> $direccion['descripcion'],
                    'pertenece'=> $direccion['pertenece']['nombre'],
                    'tipo_direccion'=> $direccion['tipo_direccion']['nombre'],
                ];
                $nuevaDireccion = IntranetDireccion::create($arrayDireccion);
                array_push($direccionesAlmacenadas, $nuevaDireccion);
            
            }
            
           foreach ($contactos as $contacto) {
                $arrayContacto = [ 
                    'id_cliente'=> (integer) $nuevo->id,
                    'tipo'=> $contacto['tipo']['nombre'],
                    'pertenece'=> $contacto['pertenece']['nombre'],
                    'descripcion'=> $contacto['descripcion'],
                    'observacion'=> $contacto['observacion'],
                ];
                $nuevoContacto =IntranetContacto::create($arrayContacto);
                array_push($contactosAlmacenados, $nuevoContacto);

            }

            $data =  [ 
                "cliente" => $nuevo,
                "dirreciones" => $direccionesAlmacenadas,
                "contactos" => $contactosAlmacenados,
            ];
            $this->especificarRespuesta($item, $data);
            return $this->retornar();
    
        }
         else{
            $this->retornarError('cliente','datos del cliente no validos');
         }
    }

    public function itemDataAdicional($item){
        //cliente
        $contacto = $item->contacto()->get();
        $direccion = $item->direccion()->get();
        //contacto
        $this->especificarRespuesta('contactos', $contacto);
        $this->especificarRespuesta('direcciones', $direccion);
    }

    public function detallesClienteGet(){
        $id = Input::get('id');
        $cliente = IntranetCliente::find($id);
        $data  = null; 
        $direcciones = $cliente->direccion()->get();
        $aux = null; 
        foreach ($direcciones as $direccion) {
            $data = $direccion->barrio()->get();
            foreach ($data as $barrio) {
                # code...
                $aux =IntranetBarrio::traerBarrio($barrio->id);
                $direccion->pais = $aux->pais;
                $direccion->departamento = $aux->departamento;
                $direccion->municipio= $aux->municipio;
                $direccion->barrio = $aux->nombre;
            }
        }
        
        $this->especificarRespuesta('direccion',$direcciones );
        $this->especificarRespuesta('contactos',$cliente->contacto()->get() );
        $this->especificarRespuesta('personas', $cliente->personas()->get());
        $this->especificarRespuesta('empresas', $cliente->empresas()->get());
        $this->especificarRespuesta('presolicitudes', $cliente->presolicitudes()->get());
        return $this->retornar();

    }

    public static function infoClientes($inicio, $final,$contactos,$presolicitudes,$creditos,$direccion,$dni,$ruc,$caracterNombre){
        if(isset($inicio) && isset($final)){
            $clientes =  IntranetCliente::whereBetween('intranet_cliente.id', [$inicio,$final])->get();
        }elseif(isset($dni)){
            $clientes = IntranetCliente::where('dni','ILIKE','%'.$dni.'%')->get();
        }elseif(isset($ruc)){
            $clientes = IntranetCliente::where('dni','ILIKE','%'.$ruc.'%')->get();
        }
        elseif(isset($caracterNombre)){
            $clientes = IntranetCliente::where('nombre','ILIKE','%'.$caracterNombre.'%')->get();
        }
        else{
            $clientes = IntranetCliente::all();
        }
        
        foreach($clientes as $cliente){
            if($contactos){
                $cliente->contactos = $cliente->contacto()->get();
            }
            if($presolicitudes){
                $cliente->presolicitudes = $cliente->presolicitudes()->get();
                foreach( $cliente->presolicitudes as $presolicitud){
                    $presolicitud->producto = $presolicitud->producto()->first();
                }
            }
            if($creditos){
                $cliente->creditos = $cliente->creditos()->get();
            }
            if($direccion){
                $cliente->direccion = $cliente->direccion()->get();
            }
        }
        return $clientes;
    }

    /** API **/

    public static function clientesGet()
    {
        $clientes=IntranetCliente::with('contacto','direccion', 'presolicitudes')->get();

        return $clientes;
    }

    public static function clienteGet($id)
    {
        $cliente = IntranetCliente::with('contacto','direccion', 'presolicitudes')->find($id);

        return $cliente;
    }

    public static function apiRegistrar($registro,$usuario)
    {
        //dd($usuario);
        $validarCliente = new IntranetCliente;
        $validacionCliente = Validator::make(
            $registro,
            $validarCliente->reglasValidacion(null,0)
        );
        if($validacionCliente->passes()){
            $client = IntranetCliente::create([
                'nombre' => $registro['nombre'],
                'id_sucursal' => $registro['id_sucursal'],
                'id_tipo' => $registro['id_tipo'],
                'fecha_registro' => now(),
                'id_usuario' => $usuario['id'],
                'dni' => $registro['dni']
            ]);
            $client->direccion()->createMany($registro['direcciones']);
            $client->contacto()->createMany($registro['contacto']);
            /*if(!$client){
                return false;
            }*/
            return $client;
        }
        return false;
    }

    public static function apiEditar($data,$id)
    {
        //dd($data);
        $validarCliente = new IntranetCliente;
        $validacionCliente = Validator::make(
            $data,
            $validarCliente->reglasValidacion(null,0)
        );
        if($validacionCliente->passes()){
            /*if($cliente['dni'] === null && $cliente['ruc'] === null){
                return false;
               }
            $cliente['id_usuario'] = $usuario->id;*/
            $client = IntranetCliente::where('id', $id)
                    ->update(array(
                        'nombre' => $data['nombre'],
                        //'id_sucursal' => $data['id_sucursal'],
                        'id_tipo' => $data['id_tipo'],
                        //'id_usuario' => $data['id_usuario'],
                        'dni' => $data['dni']
                    ));
            /*if(!$client){
                return false;
            }*/
            //return $client;
            return response()->json(['Registro actualizado'=> $client],200);
        }
        return false;
    }

    public static function apiEliminar($id)
    {

    }

    /** Validaciones **/
    public function antesdeValidar()
    {
        if (!$this->validarNombre()) {
            return false;
        }
        return true;
    }

    private function validarNombre()
    {
        $nombre = Input::get('nombre');
        if (empty($nombre)) {
            $this->retornarError('No se ha especificado un nombre.', 'nombre');
            return false;
        }

        if (is_numeric($nombre)) {
            $this->retornarError('El nombre no es un valor válido.', 'nombre');
            return false;
        }

        return true;
    }

}
