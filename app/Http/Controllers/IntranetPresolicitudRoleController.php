<?php

namespace App\Http\Controllers;

use App\IntranetPresolicitudRole;
use App\User;
use App\IntranetMovimientoPresolicitud;
use App\IntranetPresolicitud;
use App\IntranetEtapaPresolicitud;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class IntranetPresolicitudRoleController extends Controlador
{   
    protected $modelo = 'IntranetPresolicitudRole';
    public function prePost() {
     
        $user = Auth::user();
        if(  $user->esAdmin()){
            
            return true;
        }
        $id = Input::get('id_presolicitud');
        $data = IntranetPresolicitudRole::where([['id_presolicitud','=',$id],['id_usuario','=',$user->id]])->first();
        if((integer) $data['role'] === 2 ){
            return true;
        }else{
            $this->retornarError("error, su rol no le permite generar esta acción");
            return false;
        }
    } 


    public function crearRolUsuarioPost() {
        $etapa = IntranetEtapaPresolicitud::where([['id_presolicitud','=',
        (integer)Input::get('id_presolicitud')],['estatus','=',0]])->first();
        $user = User::find(Input::get('id_usuario'));
       $role= IntranetPresolicitudRole::create([
            'id_presolicitud'=>(integer)Input::get('id_presolicitud'),
            'id_usuario'=>Input::get('id_usuario'),
            'role'=>Input::get('id_role')
        ]);
              
        $this->especificarRespuesta('roleCreate',$role);
        $this->crearMovimiento((integer)$etapa->id,"EL usuario: ".$user->nombre."obtuvo un rol, de parte del usuario :");
        return $this->retornar();
     
    }
    public function eliminarRolUsuarioPost() {
        $etapa = IntranetEtapaPresolicitud::where([['id_presolicitud','=',
        (integer)Input::get('id_presolicitud')],['estatus','=',0]])->first();
        $this->especificarRespuesta('etapa',$etapa);

        $user = User::find(Input::get('id_usuario'));
        $usuarioRol = IntranetPresolicitudRole::where([['id_usuario','=',(integer) Input::get('id_usuario')],
        ['id_presolicitud','=',(integer)Input::get('id_presolicitud')],
        ['role','=',(integer)Input::get('id_role')]])->first();
        $usuarioRol->eliminar();
        $this->crearMovimiento($etapa->id,"La accion de eliminar fue ejecutada en : ".$user->nombre."Por el usuario :");
        $this->especificarRespuesta('elimanado','ok');
   
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
          'movimiento'=> (isset($movimiento))? $movimiento." ".$this->getRole().": ".$usuario->nombre : 'creacion-credito por'.$this->getRole().":".$usuario->nombre  
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
