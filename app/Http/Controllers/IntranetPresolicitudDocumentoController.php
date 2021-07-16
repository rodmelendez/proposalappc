<?php

namespace App\Http\Controllers;

use App\IntranetPresolicitudDocumento;
use App\IntranetPresolicitudDocumentoDetalle;
use App\IntranetMovimientoPresolicitud;
use App\IntranetEtapaPresolicitud;
use App\User;
use App\IntranePresolicitud;
use App\IntranetDocumentoCategoria;
use App\IntranetPresolicitudRole;
use App\IntranetPresolicitud;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

//me siento sucio por colocar este nombre en un controlador,
//persona del futuro, si estas leyendo esto quiero que sepas que no fue mi culpa
//me vi en la obligacion de hacerlo, de verdad lo lamento :c, solo esperos que me entiendas
//fueron las politicas de la empresa en ese entonces
//att: programador anonimo que te aprecia, programador del futuro
class IntranetPresolicitudDocumentoController extends Controlador
{
    protected $modelo = 'IntranetPresolicitudDocumento';

    public function prePost() {
        // ,1 ejecutivo(solo subir documentos, imagenes y observaciones),2)supervisores crud + crear roles
        //3) comite, solo puede ver,  4) comite, todos los permisos menos borrar 
        $user = Auth::user();
        $id = Input::get('id_presolicitud');
        $data = IntranetPresolicitudRole::where([['id_presolicitud','=',$id],['id_usuario','=',$user->id],['fecha_eliminacion','=',null]])->first();
        if(  $user->esAdmin()){
            
            return true;
        }
        if((integer) $data['role'] === 3 ){
          $this->retornarError("error, los usuarios con el role comite no puede intereactuar directamente con la presolicitud");
        }
        return true;
        //return ((integer)$data->role === 3)? false: ;
    }

    public function subirImagenPost() {
        $nombre_foto = ImagenController::subirImagenParaItem(null, 'foto');

        if (empty($nombre_foto)) {
            return $this->retornarError('Error. No se pudo guardar la imagen.');
        }

        $atributos = ImagenController::atributos($nombre_foto);

        $this->especificarRespuesta('nombre', $nombre_foto);
        $this->especificarRespuesta('atributos', $atributos);

        return $this->retornar();
     }

    public function subirArchivoPost() {
        $nombre_archivo = ArchivoController::subirArchivoParaItem(null, 'archivo');
        $this->especificarRespuesta('nombre', $nombre_archivo);
        return $this->retornar();
    }
    
    public function despuesDeGuardar($item) {
        $data = IntranetPresolicitud::find(Input::get('id_presolicitud'));
        $etapa =  $data->etapaPresolicitud()->where('estatus','=',0)->first();
        if(isset($etapa)){            
      
            $this->crearMovimiento($etapa->id,"documento subido por :");
           
        }
    
        if((integer) $item->tipo === 1){
            $observaciones = Input::get("observaciones");
            $ancho = Input::get("ancho");
            $alto = Input::get("alto");
            $latitud = Input::get("latitud");
            $longitud = Input::get("longitud");
            $kbs = Input::get("kbs");
            $camara = Input::get("camara");
            $id_usuario  = Input::get("id_usuario");
            $item->metadata = IntranetPresolicitudDocumentoDetalle::create([
                'id_presolicitud_documento'=>(integer)$item->id,
                'observaciones'=>(isset($observaciones))?$observaciones : "ninguna",
                'ancho'=>(isset($ancho))?$ancho : 600,
                'alto'=>(isset($alto))?$alto : 1080,
                'latitud'=>(isset($latitud))?$latitud : null,
                'longitud'=>(isset($longitud))?$longitud : null,
                'kb'=>(isset($kbs))?$kbs : 250,
                'camara'=>(isset($camara))?$camara : "desconocida",
                'id_usuario'=>$id_usuario
                ]);
            $this->especificarRespuesta('imagen', $item);
        }
        if((integer)$item->tipo === 2){
            $observaciones = Input::get("observaciones");
            $id_usuario  = Input::get("id_usuario");
             $item->metadata = IntranetPresolicitudDocumentoDetalle::create([
            'id_presolicitud_documento'=>(integer)$item->id,
            'observaciones'=>(isset($observaciones))?$observaciones : "ninguna",
            'id_usuario'=>$id_usuario
            ]);
        }

    }
    public function addObservacionPost()
    {
        IntranetPresolicitudDocumentoDetalle::where('id_presolicitud_documento','=',Input::get('id'))
        ->update([
            'observaciones'=>Input::get("observacion")
        ]);
        $documento = IntranetPresolicitudDocumento::find(Input::get('id'));
        $documento->metadata = $documento->metadata()->first();
        $this->especificarRespuesta('observacion', $documento);
    }
    public function itemDataAdicional($item) {
        //cliente
        $cliente = $item->cliente()->get();
        $usuario = $item->usuario()->get();
        //contacto
        $this->especificarRespuesta('cliente', $cliente);
        $this->especificarRespuesta('usuario', $usuario);
    }

    public function rotarImagenPost(){
        $nombre = Input::get("nombre");
        $id = Input::get("id");
        ImagenController::rotar($nombre);
        $item = IntranetPresolicitudDocumento::find($id);
        $this->especificarRespuesta('imagen', $item);
        return $this->retornar();
    }

    public function crearMovimiento($id_etapa,$movimiento = null){
        $descripcion = Input::get('observacion'); 
        $usuario = Auth::user();
         IntranetMovimientoPresolicitud::create([
          'id_usuario'=> (integer) $usuario->id,
          'id_etapa_presolicitud'=>$id_etapa,               
          'descripcion'=>(isset($descripcion))?$descripcion : "ninguna",
          'fecha'=>Input::get('fecha'),
          'movimiento'=> (isset($movimiento))? $movimiento.$this->getRole().":".$usuario->nombre : 'creacion-credito por'.$this->getRole().":".$usuario->nombre  
          ]);
    }
      public function getRole()
      {
        $user = Auth::user();
        $id = Input::get('id_presolicitud');
        $data = IntranetPresolicitudRole::where([['id_presolicitud','=',$id],['id_usuario','=',$user->id]])->first();
        if(  $user->esAdmin()){   
            return 'admin';
        } 
        if( $data['role']=== 1){
            return "ejecutivo ";
        }
        if( $data['role']=== 2){
            return "supervisor ";
        }
        if( $data['role']=== 4){
            return "operaciones ";
        }
       
      }
}
