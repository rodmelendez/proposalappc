<?php

namespace App\Http\Controllers;

use App\IntranetPresolicitud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\IntranetCliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\IntranetDocumentoCategoria;
use App\IntranetDocumento;
use App\IntranetPresolicitudDocumento;
use App\IntranetEtapaPresolicitud;
use App\IntranetMovimientoPresolicitud;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\IntranetPresolicitudRole;
use App\Permiso;
use App\UsuariosPermisos;
use App\User;
use App\IntranetPresolicitudProducto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class IntranetPresolicitudController extends Controlador{

    protected $modelo = 'IntranetPresolicitud';

    public function presolicitudesGet()
    {
      $presolicitudes = IntranetPresolicitud::traerPresolicitudes();
      foreach($presolicitudes as $presolicitud){
        $presolicitud->etapas = $presolicitud->etapaPresolicitud()->get();
      }
      $this->especificarRespuesta('presolicitudes',$presolicitudes);
      return $this->retornar();
    }

    public function cargarListadosGet()
    {
        $clientes = IntranetCliente::all();
        $descripcion = 'clientes';
        $documentos = new IntranetDocumentoCategoriaController;
        $descripcion2 = 'documentos';
        $data = Permiso::where([['categoria','=','presolicitud'],['nombre','=','todos']])->first();
        $usuarios = array();
        if($data){
            $permitidos = UsuariosPermisos::where('id_permiso','=',$data->id)->get();
            foreach ($permitidos as $permitido) {
                array_push($usuarios,$permitido->usuario()->first());
            }
            $this->especificarRespuesta($descripcion,$clientes);
            $this->especificarRespuesta($descripcion2,$documentos->indexGet());
            $this->especificarRespuesta("usuariosPermitidos", $usuarios);
            $this->especificarRespuesta("productos",IntranetPresolicitudProducto::all());
            //$this
        }else {
            $this->retornarError("error,modulo no encontrado");

        }
        return $this->retornar();
    }
     
    public function itemDataAdicional($item)
    {
        $cliente = $item->cliente()->get()->first();
        $usuario = $item->usuario()->get()->first();
        $etapas = $item->etapaPresolicitud()->get();
        $sucursal = $item->sucursal()->first();
        $responsables = $item->presolicitudRoles()->get();
        foreach($responsables as $responsable){
            $responsable->detalle = $responsable->usuario()->first();
        }
        $this->informacionCarpetas($item->id);
        foreach ($etapas as $etapa) {
            $etapa->movimientos= $etapa->movimientos()->get();
            foreach ($etapa->movimientos as $movimiento) {
                $movimiento->usuario = $movimiento->usuario()->first();
            }
        }
        $this->especificarRespuesta('cliente', (object)$cliente);
        $this->especificarRespuesta('usuario', (object)$usuario);
        $this->especificarRespuesta('nombreSucursal', $sucursal->nombre);

        $this->especificarRespuesta('etapas',$etapas);
        $this->especificarRespuesta('usuariosResponsables',$responsables);
    }
     
    public function documentosPorCarpetaGet()
    {
      $id_presolicitud = Input::get('id');
      $id_carpeta = Input::get('id_carpeta');
      $this->especificarRespuesta('ingreso',$id_carpeta);
      if($id_carpeta !== null){

        $this->infromacionCarpeta($id_presolicitud,(Int)$id_carpeta);
      }else {

        $this->informacionCarpetas($id_presolicitud);
      }
      return $this->retornar();
    }

    public function informacionCarpetas($id_presolicitud)
    {
      $carpetaPadre = self::carpetasPresolicitud($id_presolicitud, null);
      $this->especificarRespuesta('carpetas',$carpetaPadre);
      $this->especificarRespuesta('genericos',[
        'foto_generico'=>IntranetDocumento::find(2),
        'pdf_generico'=>IntranetDocumento::find(1),
        'id_carpeta'=>6
      ]);
      return $this->retornar();
    }

    public function infromacionCarpeta($id_presolicitud,$id_carpeta)
    {
      $carpetaPadre = self::carpetasPresolicitud($id_presolicitud,$id_carpeta);
      
      $this->especificarRespuesta('carpetas',$carpetaPadre);
      $this->especificarRespuesta('genericos',[
        'foto_generico'=>IntranetDocumento::find(2),
        'pdf_generico'=>IntranetDocumento::find(1),
        'id_carpeta'=>6,
        'documentosPresolicitudGenericos'=> IntranetPresolicitudDocumento::where([
          ['id_presolicitud','=',$id_presolicitud],
          ['id_carpeta','=',6],
          ['fecha_eliminacion','=',null]])->get(),
      ]);
      return $this->retornar();
    }

    public function despuesDeGuardar($item)
    {
        $usuarios = json_decode(Input::get('user-role'));
        $asignados = array();
        if($usuarios !== null){
            foreach ($usuarios  as $usuario) {
                array_push($asignados,IntranetPresolicitudRole::create([
                    'id_presolicitud'=>$item->id,
                    'id_usuario'=>$usuario->id_usuario,
                    'role'=>$usuario->role
                ]));
            }
            $this->especificarRespuesta('usuariosResponsables',$asignados);
        }
        $etapa =  $item->etapaPresolicitud()->where('estatus','=',0)->first();

        if(isset($etapa))
        {

            $duracion = Input::get('duracion');
            $movimiento = Input::get('movimiento');
            if($movimiento === "retroceder"){
                if( (integer) $item->estado_etapa === 1){
                    return $this->retornarError("error, no puede retroceder mas");

                }
                $etapa->update(['estatus'=>1,'duracion'=>$duracion]);
                $this->crearMovimiento($etapa->id,"Etapa de presolicitud aprobada por ");
                $nuevaEtapa = $this->crearEtapa($item->id,(integer) $item->estado_etapa - 1);
                $this->especificarRespuesta('nuevaEtapa',$nuevaEtapa);
                // $carpetaPadre = IntranetDocumentoCategoria::where('id','=',$item->estado_etapa)->first();
                // $carpetaPadre->subCarpetas = $carpetaPadre->carpetasHijos()->get();
                // $documentos = IntranetPresolicitudDocumento::where([['id_presolicitud','=',$item->id],['id_carpeta','=',$item->estado_etapa],['fecha_eliminacion','=',null]])->get();
                // foreach ($documentos as $documento) {
                //     $documento->eliminar();
                // }

                // foreach ($carpetaPadre->subCarpetas as $subCarpeta) {
                //   $documentos = IntranetPresolicitudDocumento::where([['id_presolicitud','=',$item->id],['id_subcarpeta','=',$subCarpeta->id],['fecha_eliminacion','=',null]])->get();
                //   foreach ($documentos as $documento) {
                //     $documento->eliminar();
                //   }
                // }
                $item->update(['estado_etapa'=> $item->estado_etapa -1 ]);
            }
            if($movimiento === "avanzar"){
                if($this->permisoChek()){
                    if( (integer) $item->estado_etapa === 5){
                        return $this->retornarError("error, no puede avanzar mas");

                    }
                    if((integer) $etapa->primerChek === 1 && (integer) $etapa->segundoChek !== 1 ){
                        $etapa->update(['estatus'=>1,'segundoChek'=>1,'duracion'=>$duracion]);
                        $nuevaEtapa = $this->crearEtapa($item->id,(integer) $item->estado_etapa + 1);
                        $this->crearMovimiento($etapa->id,"Etapa de presolicitud aprobada por ");
                        $this->especificarRespuesta('nuevaEtapa',$nuevaEtapa);
                        $item->update([
                            'estado_etapa'=> $item->estado_etapa + 1
                        ]);
                    }
                    //se valida en la primera parte para no tener que hacerlo en la segunda
                    if((integer) $etapa->primerChek !== 1){

                        // $carpetaPadre = IntranetDocumentoCategoria::where('id','=',$item->estado_etapa)->first();

                        $this->especificarRespuesta('id_estadoPresolicitud',$item->estado_etapa);
                        // if(!$this->documentosCarpeta($item->id,$item->estado_etapa)){

                        //   return $this->retornarError("En la primera carpeta debe tener todos los documentos Requeridos para poder avanzar a la siguente etapa");

                        // }else{
                        //   $aux = true;
                        //   foreach ($carpetaPadre->carpetasHijos()->get() as $subCarpeta) {
                        //     if(! $this->documentosSubCarpeta($item->id,$subCarpeta->id,$item->estado_etapa)){
                        //       $aux = false;
                        //       break;
                        //     }
                        //   }
                        //   if(!$aux){
                        //     return $this->retornarError("debe tener todos los documentos Requeridos para poder avanzar a la siguente etapa");

                        //   }
                        // }
                        //if($aux ){
                        $etapa->update(['primerChek'=>1,'duracion'=>$duracion]);
                        $this->crearMovimiento($etapa->id,"Etapa de presolicitud, revisada por ");
                        //}

                    }

                }
            }
            if($movimiento === "observacion"){
                $etapa->update(['duracion'=>$duracion]);
                $this->crearMovimiento($etapa->id,"Observacion Realizada por");
            }
            if($movimiento === "credito-rechazado"){
                $etapa->update(['estatus'=>1,'duracion'=>$duracion]);
                $this->crearMovimiento($etapa->id,"Presolicitud rechazada  por ");
                $item->update(['estado_vida'=>0]);
            }

        }else{
            $etapa = $this->crearEtapa($item->id,null);
            $this->crearMovimiento($etapa->id,null);
        }
        $etapa->movimientos= $etapa->movimientos()->get();
        foreach($etapa->movimientos as $movimiento){
            $movimiento->usuario = $movimiento->usuario()->first();
        }
        $this->especificarRespuesta('etapaAnterior',$etapa);
        $item = IntranetPresolicitud::find($item->id);
        $this->especificarRespuesta('presolicitud',$item);
        $this->retornar();
    }

    public function crearEtapa($id_presolicitud,$etapa)
    {
        $acumulado =  IntranetEtapaPresolicitud::where([['id_presolicitud','=',$id_presolicitud],['etapa','=',$etapa]])->get()->sum('duracion');
        return IntranetEtapaPresolicitud::create([
            'id_presolicitud'=>$id_presolicitud,
            'etapa'=>(isset($etapa))?$etapa:1,
            'fecha_registro'=>Input::get('fecha'),
            'duracion'=>$acumulado,
            'estatus'=>0
        ]);
    }

    public function crearMovimiento($id_etapa,$movimiento = null)
    {
        $descripcion = Input::get('observacion');
        $usuario = Auth::user();
        IntranetMovimientoPresolicitud::create([
            'id_usuario'=> (integer) $usuario->id,
            'id_etapa_presolicitud'=>$id_etapa,
            'descripcion'=>(isset($descripcion))?$descripcion : "ninguna",
            'fecha'=> Input::get('fecha'),
            'movimiento'=> (isset($movimiento))? $movimiento.$this->getRole().":".$usuario->nombre : 'creacion-credito por'.$this->getRole().":".$usuario->nombre
        ]);
    }

    public function prePost()
    {
      // ,1 ejecutivo(solo subir documentos, imagenes y observaciones),2)supervisores crud + crear roles
      //3) comite, solo puede ver,  4)operaciones, todos los permisos menos borrar 
      $user = Auth::user();
      $id = Input::get('id');
      $data = IntranetPresolicitudRole::where([['id_presolicitud','=',$id],['id_usuario','=',$user->id],['fecha_eliminacion','=',null]])->first();
      
      if(  $user->esAdmin()){   
        return true;
      }
      if( $data && isset($data) )
        if((integer) $data['role'] === 3 ){
          $this->retornarError("error, los usuarios con el role comite y de operaciones no puede intereactuar directamente con la presolicitud");
        }
      return true;
      //return ((integer)$data->role === 3)? false: ;
    }

    public function permisoChek()
    {
        $user = Auth::user();
        $id = Input::get('id');
        $data = IntranetPresolicitudRole::where([['id_presolicitud','=',$id],['id_usuario','=',$user->id]])->first();
        if((integer) $data['role'] === 3 ){
            return false;
        }
        return true;
    }

    public function getRole()
    {
        $user = Auth::user();
        $id = Input::get('id');
        $data = IntranetPresolicitudRole::where([['id_presolicitud','=',$id],['id_usuario','=',$user->id]])->first();
        if(  $user->esAdmin()){
            return 'admin';
        }
        if( $data && isset($data)){
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

    public function documentosCarpeta($id_presolicitud,$id_carpeta)
    {
        $carpetaPadre = IntranetDocumentoCategoria::where('id','=',$id_carpeta )->first();
        $documentosCar = $carpetaPadre->documentosCarpeta()->where('opcional','=',false)->count();
        $count = 0 ;

        foreach($carpetaPadre->documentosCarpeta()->where('opcional','=',false)->get() as $documentosCarp){
            foreach (IntranetPresolicitudDocumento::where([['id_presolicitud','=',$id_presolicitud],['id_carpeta','=',$id_carpeta ],['fecha_eliminacion','=',null]])->get() as $documentosPres) {
                if($documentosCarp->id === $documentosPres->id_documento){
                    $count ++;
                }
            }
        }
        if($count === $documentosCar ){
            return true;
        }else {
            return false;
        }
    }

    public function documentosSubCarpeta($id_presolicitud,$id_subcarpeta,$id_padre)
    {
        $subCarpeta = IntranetDocumentoCategoria::where('id','=',$id_subcarpeta )->first();
        $documentosCar = $subCarpeta->documentosCarpeta()->where('opcional','=',false)->count();
        $count = 0 ;

        foreach($subCarpeta->documentosCarpeta()->where('opcional','=',false)->get() as $documentosCarp){
            foreach (IntranetPresolicitudDocumento::where([['id_presolicitud','=',$id_presolicitud],['id_carpeta','=',$id_padre ],['id_subcarpeta','=',$id_subcarpeta ],['fecha_eliminacion','=',null]])->get() as $documentosPres) {
                if($documentosCarp->id === $documentosPres->id_documento){
                    $count ++;
                }
            }
        }
        if($count === $documentosCar ){
            return true;
        }else {
            return false;
        }
    }

    private static function presolicitudDocumentos($presolicitud, $carpeta)
    {
        $documentos = IntranetDocumento::
        where('id_documento_categoria', $carpeta->id)
            ->where('fecha_eliminacion', null)
            ->get();
        if ($documentos){
            foreach ($documentos as $documento) {
                $documento["archivos"] = IntranetPresolicitudDocumento::
                where('id_presolicitud', $presolicitud->id)
                    ->where('fecha_eliminacion', null)
                    ->where('id_carpeta', $carpeta->id)
                    ->where('id_documento', $documento->id)
                    ->get()->all();
            }
        }
        return $documentos ?: [];
    }

    private static function presolicitudCarpetas($presolicitud, $carpeta = null)
    {
        if (!$carpeta){
            $carpetas = IntranetDocumentoCategoria::
            where('id_documento_categoria', null)
                ->get()->all();
            $carpetasArray = [];
            foreach ($carpetas as $carpeta) {
                array_push($carpetasArray, self::presolicitudCarpetas($presolicitud, $carpeta));
            }
            return $carpetasArray;
        }else {
            $carpetas = [];
            $subCarpetas = IntranetDocumentoCategoria::
            where('id_documento_categoria', $carpeta->id)
                ->get();
            if ($subCarpetas){
                foreach ($subCarpetas as $subCarpeta) {
                    array_push($carpetas, self::presolicitudCarpetas($presolicitud, $subCarpeta));
                }
            }
            $documentos = self::presolicitudDocumentos($presolicitud, $carpeta);

            return [
                "carpeta" => $carpeta,
                "sub_carpetas" => $carpetas,
                "documentos" => $documentos,
            ];
        }
    }

    private static function camposPresolicitudes()
    {
        $campos = [
            "intranet_presolicitud.id as id",
            //"intranet_presolicitud.id_simi as id_simi",
            "intranet_presolicitud.id_cliente as id_cliente",
            "intranet_presolicitud.id_producto as id_producto",
            "intranet_presolicitud.id_usuario as id_usuario",
            "intranet_cliente.nombre as nombre_cliente",
            "intranet_presolicitud_producto.nombre as nombre_producto",
            "usuario.nombre as nombre_usuario",
            //"intranet_presolicitud.monto_solicitado as monto_solicitado",
            //"intranet_presolicitud.monto_asignado as monto_asignado",
            //"intranet_presolicitud.fecha_asignacion as fecha_asignacion",
            //"intranet_presolicitud.fecha_solicitud as fecha_solicitud",
            //"intranet_presolicitud.plazo_solicitado as plazo_solicitado",
            //"intranet_presolicitud.plazo_asignado as plazo_asignado",
            //"intranet_presolicitud.moneda as moneda",
            //"intranet_presolicitud.estado_etapa as estado_etapa",
            //"intranet_presolicitud.estado_vida as estado_vida",
            //"intranet_presolicitud.fecha_creacion as fecha_creacion",
            //"intranet_presolicitud.fecha_actualizacion as fecha_actualizacion",
            //"intranet_presolicitud.fecha_eliminacion as fecha_eliminacion",
            //"intranet_presolicitud.status as status",
        ];

        return $campos;
    }

    /******* API *******/
    #IntranetDocumentoCategoria
    /*public static function presolicitudes($inicio, $final, $usuarioprueba)
    {
       // dd($usuarioprueba);
        /*if(isset($inicio) && isset($final))
        {
            $presolicitudes =  IntranetPresolicitud::
            leftJoin('intranet_cliente', 'intranet_presolicitud.id_cliente', 'intranet_cliente.id')
                ->leftJoin('intranet_presolicitud_producto', 'intranet_presolicitud.id_producto', 'intranet_presolicitud_producto.id')
                ->leftJoin("usuario", "intranet_presolicitud.id_usuario", "usuario.id")
                //->whereBetween('intranet_presolicitud.id', [$inicio,$final])
                //->where("usuario.nombre", $usuario->nombre)
                ->get(self::camposPresolicitudes());
            return $presolicitudes;
        }
        $presolicitudes = IntranetPresolicitud::
        leftJoin('intranet_cliente', 'intranet_presolicitud.id_cliente', 'intranet_cliente.id')
            ->leftJoin('intranet_presolicitud_producto', 'intranet_presolicitud.id_producto', 'intranet_presolicitud_producto.id')
            ->leftJoin("usuario", "intranet_presolicitud.id_usuario", "usuario.id")
            ->where("usuario.nombre", $usuarioprueba)
            ->get(self::camposPresolicitudes());
        return $presolicitudes;
    }*/

    public static function carpetasPresolicitud($id_presolicitud,$id_carpeta)
    {
        if($id_carpeta !== null){
            $carpetasPadre = IntranetDocumentoCategoria::where('id','=',$id_carpeta)->first();
            $carpetasPadre->subCarpetas = $carpetasPadre->carpetasHijos()->where('fecha_eliminacion','=',null)->get();
            $carpetasPadre->documentos =  $carpetasPadre->documentosCarpeta()->where('fecha_eliminacion','=',null)->get();
            foreach ($carpetasPadre->documentos as $documento) {
                $documento->detalle = self::documentoDetalle($id_presolicitud,$documento->id);
            }
            foreach ($carpetasPadre->subCarpetas as $subCarpeta) {
                $subCarpeta->documentos = $subCarpeta->documentosCarpeta()->where('fecha_eliminacion','=',null)->get();
                foreach ($subCarpeta->documentos as $documento) {
                    $documento->detalle = self::documentoDetalle($id_presolicitud,$documento->id);
                }
            }
        }else {
            $carpetasPadre = IntranetDocumentoCategoria::where('id_documento_categoria','=',null)->get();
            foreach ($carpetasPadre as $carpetaPadre) {
                $carpetaPadre->subCarpetas = $carpetaPadre->carpetasHijos()->where('fecha_eliminacion','=',null)->get();
                $carpetaPadre->documentos =  $carpetaPadre->documentosCarpeta()->where('fecha_eliminacion','=',null)->get();
                if((integer)$carpetaPadre->id === 6 ){
                    $carpetaPadre->documentos = IntranetPresolicitudDocumento::where([
                        ['id_presolicitud','=',$id_presolicitud],
                        ['id_carpeta','=',6],
                        ['fecha_eliminacion','=',null]])->get();
                    foreach ($carpetaPadre->documentos as $documento) {
                        $documento->detalle = self::documentoDetalle($id_presolicitud,$documento->id);
                    }
                }
                foreach ($carpetaPadre->documentos as $documento) {
                    $documento->detalle = self::documentoDetalle($id_presolicitud,$documento->id);
                }
                foreach ($carpetaPadre->subCarpetas as $subCarpeta) {
                    $subCarpeta->documentos = $subCarpeta->documentosCarpeta()->where('fecha_eliminacion','=',null)->get();
                    foreach ($subCarpeta->documentos as $documento) {
                        $documento->detalle = self::documentoDetalle($id_presolicitud,$documento->id);
                    }
                }
            }
        }
        return $carpetasPadre;
    }

    public static function documentoDetalle($id_presolicitud,$id_documento)
    {
        $documentoAlamacenado = IntranetPresolicitudDocumento::where([['id_presolicitud',$id_presolicitud],['id_documento','=',$id_documento],['fecha_eliminacion','=',null]])->first();
        if($documentoAlamacenado){
            $documentoAlamacenado->metadata = $documentoAlamacenado->metadata()->first();
            if($documentoAlamacenado->tipo === 1){
                $documentoAlamacenado->url = ImagenController::urlImagen($documentoAlamacenado->nombre);
            }
            if($documentoAlamacenado->tipo === 2){
                $documentoAlamacenado->url = ArchivoController::urlDocumento($documentoAlamacenado->nombre);
            }
        }
        return $documentoAlamacenado;
    }

    public static function apiPresolicitudesGet($usuario)
    {
        //dd($usuario);
        /*if(isset($inicio) && isset($final))
        {
            $presolicitudes =  IntranetPresolicitud::
            leftJoin('intranet_cliente', 'intranet_presolicitud.id_cliente', 'intranet_cliente.id')
                ->leftJoin('intranet_presolicitud_producto', 'intranet_presolicitud.id_producto', 'intranet_presolicitud_producto.id')
                ->leftJoin("usuario", "intranet_presolicitud.id_usuario", "usuario.id")
                //->whereBetween('intranet_presolicitud.id', [$inicio,$final])
                //->where("usuario.nombre", $usuario->nombre)
                ->get(self::camposPresolicitudes());
            return $presolicitudes;
        }*/
        $presolicitudes = IntranetPresolicitud::
        leftJoin('intranet_cliente', 'intranet_presolicitud.id_cliente', 'intranet_cliente.id')
            ->leftJoin('intranet_presolicitud_producto', 'intranet_presolicitud.id_producto', 'intranet_presolicitud_producto.id')
            ->leftJoin("usuario", "intranet_presolicitud.id_usuario", "usuario.id")
            ->where("intranet_presolicitud.id_usuario", $usuario->id)
            ->get(self::camposPresolicitudes());
        return $presolicitudes;
    }

    public static function apiPresolicitudGet($id)
    {
        $data = [];
        $presolicitud = IntranetPresolicitud::
        Join('intranet_cliente', 'intranet_presolicitud.id_cliente', 'intranet_cliente.id')
            ->Join('intranet_presolicitud_producto', 'intranet_presolicitud.id_producto', 'intranet_presolicitud_producto.id')
            ->Join("usuario", "intranet_presolicitud.id_usuario", "usuario.id")
            //->where("usuario.nombre", $usuario->nombre)
            ->where("intranet_presolicitud.id", $id)
            ->get(self::camposPresolicitudes())
            ->first();

        if(!$presolicitud){
            return null;
        }
        // $presolicitud->carpetas = self::carpetasPresolicitud($id_presolicitud,$id_carpeta);
        $data["presolicitud"] = $presolicitud;
        $data["carpetas"] = self::presolicitudCarpetas($presolicitud);
        return $data;
    }

    public static function apiRegistrar($presolicitud)
    {
        //dd($presolicitud);
        $validarPresolicitud = new IntranetPresolicitud;
        $validacionPresolicitud = Validator::make(
            $presolicitud,
            $validarPresolicitud->reglasValidacion(null,0)
        );
        if($validacionPresolicitud->passes()){

            //$nuevo = IntranetPresolicitud::create($Presolicitud);
            $nuevo = IntranetPresolicitud::create([
            'id_cliente' => $presolicitud['id_cliente'],
            'id_producto' => $presolicitud['id_producto'],
            'id_usuario' => $presolicitud['id_usuario'],
            'tasa_interes' => $presolicitud['tasa_interes'],
            'forma_credito' => $presolicitud['forma_credito'],
            'descripcion' => $presolicitud['descripcion'],
            'direccion' => $presolicitud['direccion'],
            'monto_solicitado' => $presolicitud['monto_solicitado'],
            'fecha_solicitud' => now(),
            'plazo_solicitado' => $presolicitud['plazo_solicitado'],
            'moneda' => $presolicitud['moneda']
            ]);

            if(!$nuevo){
                return false;
            }
            return $nuevo;
            /*$data=array("id_cliente" => Input::get('id_cliente'),"id_producto" => Input::get('id_producto'),"id_usuario"=> Input::get('id_usuario'),
                        "tasa_interes"=> Input::get('tasa_interes'), "forma_credito" => Input::get('forma_credito'), "descripcion" => Input::get('descripcion'),
                        "direccion" => Input::get('direccion'), "monto_solicitado" => Input::get('monto_solicitado'), "fecha_solicitud" => $fecha_solicitud,
                        "plazo_solicitado" => Input::get('plazo_solicitado'), "moneda" => Input::get('moneda'), "estado_etapa" => Input::get('estado_etapa'));
            //dd($data);
            DB::table('intranet_presolicitud')->insert($data);*/

        }
        return false;
    }

    public static function apiEditar($presolicitud , $id)
    {
        $validarPresolicitud = new IntranetPresolicitud;
        $validacionPresolicitud = Validator::make(
            $presolicitud,
            $validarPresolicitud->reglasValidacion(null,0)
        );
        if($validacionPresolicitud->passes()){

            //$registroupdate = IntranetPresolicitud::findOrFail($id);
            DB::table('intranet_presolicitud')
                ->where('id', $id)
                ->update(array( 'forma_credito' => Input::get('forma_credito'),
                                'descripcion' => Input::get('descripcion'),
                                'direccion'   => Input::get('direccion'),
                                'monto_solicitado'   => Input::get('monto_solicitado'),
                                'monto_asignado'   => Input::get('monto_asignado'),
                ));

            return response()->json(['Registro actualizado'],200);
        }
        return false;
    }

}