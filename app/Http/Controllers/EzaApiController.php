<?php

namespace App\Http\Controllers;


use App\Empresa;
use App\Empleado;
use App\IntranetCliente;
use App\IntranetContacto;
use App\IntranetDireccion;
use App\Persona;
use App\User;
use App\UsuarioTouchId;
use App\IntranetPresolicitudProducto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;

class EzaApiController   {

    public function iniciarSesionPost() {
        $usuario = $this->iniciarSesionPorImei();

        if (!$usuario) {
            $nombre = Input::get('nombre');
            $contrasena = Input::get('contrasena');

            if (empty($nombre) || empty($contrasena)) {
                return $this->error('Valores inválidos.');
            }

            $usuario = $this->iniciarSesion($nombre, $contrasena);

            if ($usuario === false) {
                return $this->error('Valores incorrectos.');
            }

            $this->registrarSesionPorImei($usuario);
        }

        return self::jsonUsuario($usuario);
    }

    public function iniciarSesionPorImei() {
        $imei = Input::get('imei');
        $serial = Input::get('serial');

        if (empty($imei) || empty($serial)) {
            return false;
        }

        $u_touchid = UsuarioTouchId::where('imei', '=', $imei)
            ->where('serial_dispositivo', '=', $serial)
            ->first();

        if (!$u_touchid) {
            return false;
        }

        $usuario = User::find((int)$u_touchid->id_usuario);
        return $usuario;

        //return $this->iniciarSesionPorUsuario($usuario);
    }

    public function registrarSesionPorImei($usuario) {
        $imei = Input::get('imei');
        $serial = Input::get('serial');

        if (empty($imei) || empty($serial)) {
            return false;
        }

        if (!($empleado = Empleado::deUsuario($usuario))) {
            return false;
        }

        UsuarioTouchId::create([
            'id_usuario' => $usuario->id,
            'id_empleado' => $empleado->id,
            'imei' => $imei,
            'serial_dispositivo' => $serial,
        ]);

        return true;
    }

    public function iniciarSesion($nombre, $contrasena) {
        $usuario = User::where('nombre', '=', $nombre)->first();
        if ($usuario) {
            if (Hash::check($contrasena, $usuario->contrasena)) {
                return $this->iniciarSesionPorUsuario($usuario);
            }
        }
        return false;
    }

    public function iniciarSesionPorUsuario($usuario) {
        $token = Str::random(60);

        $usuario->api_token = $token;
        $usuario->fecha_ultimo_ingreso = date('Y-m-d H:i:s');
        $usuario->save();

        return $usuario;
    }

    public function buscarUsuarioPorToken($token) {
        if (empty($token)) return null;
        return User::where('api_token', '=', $token)->first();
    }

    /**
     * @param \App\User $usuario
     * @return array
     */
    public static function jsonUsuario($usuario) {
        $persona = $usuario->traerPersona();

        $empleado = $persona ? $persona->empleado() : null;

        //$empresa = $empleado ? Empresa::find((int)$empleado->id_empresa) : null;
        $empresas = $empleado ? $empleado->empresas()->get() : [];

        $empresa = null;
        $data_empresas = [];
        if (count($empresas)) {
            foreach ($empresas as $empresa) {
                $data_empresas[] = [
                    'id' => $empresa->id,
                    'nombre' => $empresa->nombre,
                    'ubicacion' => $empresa->ubicacion ?: '',
                    'logo' => EmpresaController::urlLogo($empresa->logo),
                    'color' => $empresa->color ?: '',
                    'color_fondo' => $empresa->color_fondo ?: '',
                    'telefono' => $empresa->telefono ?: '',
                    'website' => $empresa->website ?: '',
                ];
            }
        }

        return [
            'token' => $usuario->api_token,
            'num_control' => $empleado ? $empleado->num_control : '',
            'nombre' => $persona ? $persona->nombres() : $usuario->nombre,
            'foto' => PersonaController::urlFoto($persona ? $persona->foto : null),
            'fecha_ingreso' => $empleado ? $empleado->fecha_ingreso : '',
            'tipo_cargo' => $empleado ? $empleado->tipo_cargo : '',
            'empresas' => $data_empresas,
        ];
    }

    private function error($mensaje = null) {
        $arr = ['token' => ''];
        if (!empty($mensaje)) {
            $arr['mensaje'] = $mensaje;
        }
        return $arr;
    }

    /** nuevo codigo para la API **/

    public function catalogos()
    {
        $pais = IntranetBarrioController::catalogo();
        return response()->json([
            'pais'=>$pais,
            'productos'=>IntranetPresolicitudProducto::all(),
            'carpetas'=>IntranetDocumentoCategoriaController::cargarCarpetasGet()
        ],200);
    }

    public function cargarSubCarpeta()
    {
        $id = Input::get('id_documento_categoria');
        $data = IntranetDocumentoCategoriaController::cargarSubcarpetaGet($id);
        if($data){

            return response()->json(['subCarpeta'=>$data ],200);
        }
        return response()->json(['error'=> 'error carpeta no encontrada' ],404);
    }

    public function validarUsuario($modelo,$token = null)
    {
        if ($token === null) {
            $token = Input::get('token');
        }
        $usuario= $this->buscarUsuarioPorToken($token);
        if(!$usuario){
            return false;
        }
        if(User::tienePermiso('crear',$modelo,$usuario)){
            return $usuario;
        }
        return null;
    }

    /** API GET **/

    public function clientes($token = null)
    {
         if ($token === null) {
             $token = Input::get('token');
         }
         if (!($usuario = $this->buscarUsuarioPorToken($token))) {
             return $this->error('Token inválido.');
         }

        /*$clientes = IntranetClienteController::infoClientes(
            Input::get('inicio'),
            Input::get('final'),
            Input::get('contactos'),
            Input::get('presolicitudes'),
            Input::get('creditos'),
            Input::get('direccion'),
            Input::get('dni'),
            Input::get('ruc'),
            Input::get('caracterNombre')
        );*/
        $clientes = IntranetClienteController::clientesGet();
        return response()->json(['clientes'=>$clientes],200);
    }

    public function cliente($id)
    {
        $token =Input::get('token');
        $usuario= $this->buscarUsuarioPorToken($token);
        //dd($usuario);
        if ($usuario)
        {
            $cliente = IntranetClienteController::clienteGet($id);

            if($cliente)
            {
                return response()->json(['cliente'=>$cliente],200);
            }
            return response()->json(['error'=>'presolicitud no encontrada'],404);
        }
        return response()->json(['error'=>'usuario no encontrado', 'usuario'=>$usuario], 404);

    }

    public function productos($token = null)
    {
        if ($token === null) {
            $token = Input::get('token');
        }
        if (!($usuario = $this->buscarUsuarioPorToken($token))) {
            return $this->error('Token inválido.');
        }

        /*$clientes = IntranetClienteController::infoClientes(
            Input::get('inicio'),
            Input::get('final'),
            Input::get('contactos'),
            Input::get('presolicitudes'),
            Input::get('creditos'),
            Input::get('direccion'),
            Input::get('dni'),
            Input::get('ruc'),
            Input::get('caracterNombre')
        );*/
        $productos = IntranetPresolicitudProductoController::productosGet();
        return response()->json(['productos'=>$productos],200);
    }

    public function presolicitudes($token = null)
    {
         if ($token === null) {
             $token = Input::get('token');
         }
         if (!($usuario = $this->buscarUsuarioPorToken($token))) {
             return $this->error('Token inválido.');
         }

        $presolicitud = IntranetPresolicitudController::apiPresolicitudesGet($usuario);

        return response()->json(['presolicitudes'=>$presolicitud],200);


        /*$usuario = $this->validarUsuario('IntranetPresolicitud');
        //dd($usuario);
        if ($usuario)
        {
            //dd($usuario);
            $data =IntranetPresolicitudController::presolicitudes($usuario);
                //Input::get('inicio'),
                //Input::get('final'),
                //Input::get('usuarioprueba'),

            if($data)
            {
                return response()->json(['presolicitudes'=>$data],200);
            }
            return response()->json(['error'=>'presolicitud no encontrada'],404);
        }
        return response()->json(['error'=>'usuario no encontrado', 'usuario'=>$usuario], 404);*/
    }

    public function presolicitud($id)
    {
        // if ($token === null) {
        //     $token = Input::get('token');
        // }
        // if (!($usuario = $this->buscarUsuarioPorToken($token))) {
        //     return $this->error('Token inválido.');
        // }
        /*$usuario = User::where('nombre', Input::get('nombre_usuario'))->first();
        if($usuario){
            $data =IntranetPresolicitudController::presolicitudGet(
                Input::get('id'),
                Input::get('id_carpeta'),
                $usuario
            );
            if($data){
                return response()->json(['data'=>$data],200);
            }
            return response()->json(['error'=>'presolicitud no encontrada'],404);
        }
        return response()->json(['error'=>'usuario no encontrado'], 404);*/
        $token =Input::get('token');
        $usuario= $this->buscarUsuarioPorToken($token);
        //dd($usuario);
        if ($usuario)
        {
            $presolicitud = IntranetPresolicitudController::apiPresolicitudGet($id);

            if($presolicitud)
            {
                return response()->json(['presolicitud'=>$presolicitud],200);
            }
            return response()->json(['error'=>'presolicitud no encontrada'],404);
        }
        return response()->json(['error'=>'usuario no encontrado', 'usuario'=>$usuario], 404);
    }

    /** API POST **/

    public function clientePost(Request $request)
    {
        //dd($request);
        $usuario = $this->validarUsuario('IntranetCliente');
        //dd($usuario);
        if($usuario){

            $registro = IntranetClienteController::apiRegistrar($request->except('token'),$usuario);

            if(!$registro){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$registro],200);
        }
        else {

            return response()->json(['error'=>'permiso denegado'],400);
        }

        /*try {
            DB::transaction(function() use ($request) {

                $cliente = IntranetCliente::create([
                    'nombre' => $request->nombre,
                    'id_tipo' => $request->id_tipo,
                    'fecha_registro'=> $request->fecha_registro,
                    'dni'=>$request->dni,
                    'id_usuario'=>$request->id_usuario,
                ]);
                $id_cliente=$cliente->id;
                //dd($request);
                $direccion = IntranetDireccion::create([
                    'id_cliente' => $id_cliente,
                    'id_barrio' => $request->id_barrio,
                    'descripcion' => $request->descripcion,
                    'pertenece' => $request->pertenece,
                    'tipo_direccion' => $request->tipo_direccion
                ]);
                $direccion->IntranetClientes()->save($cliente);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
        return response()->json(['message' => 'Success']);*/
    }

    public function direccionPost(Request $request)
    {

        $usuario = $this->validarUsuario('IntranetDireccion');
        //dd($usuario);
        if($usuario){

            $data = IntranetDireccionController::apiRegistrar($request->except('token'), $usuario);
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

    public function contactoPost(Request $request)
    {
        if($this->validarUsuario('IntranetContacto')){

            $data = IntranetContactoController::apiRegistrar($request->except('token'));
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

    public function personaPost(Request $request)
    {
        if($this->validarUsuario('IntranetPersona')){

            $data = IntranetPersonaController::apiRegistrar($request->except('token'));
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

    public function empresaPost(Request $request)
    {
        if($this->validarUsuario('IntranetEmpresa')){

            $data = IntranetEmpresaController::apiRegistrar($request->except('token'));
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

    public function presolicitudPost(Request $request)
    {
        //dd($request);
        $usuario = $this->validarUsuario('IntranetPresolicitud');
        if($usuario){

            $data = IntranetPresolicitudController::apiRegistrar($request->except('token'), $usuario);
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

    /**** API PUT ****/

    public function clientePut(Request $request, $id)
    {
        if($this->validarUsuario('IntranetCliente')){

            $data = IntranetClienteController::apiEditar($request->except('token'),$id);
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

    public function presolicitudPut(Request $request, $id)
    {
        if($this->validarUsuario('IntranetPresolicitud')){

            $data = IntranetPresolicitudController::apiEditar($request->except('token'),$id);
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

    public function direccionPut(Request $request, $id)
    {
        if($this->validarUsuario('IntranetPresolicitud')){

            $data = IntranetDireccionController::apiEditar($request->except('token'),$id);
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

    public function contactoPut(Request $request, $id)
    {
        if($this->validarUsuario('IntranetPresolicitud')){

            $data = IntranetContactoController::apiEditar($request->except('token'),$id);
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

    /**** API DELETE ****/

    public function clienteDelete($id)
    {
        $cliente = DB::table('intranet_cliente')->where('id',$id)->delete();
        //$cliente = IntranetCliente::join("intranet_contacto", "intranet_cliente.id", "intranet_contacto.id_cliente")
        //->where('id', $id)->first();

        return response()->json('nota borrada',200);
    }

    public function presolicitudDelete($id)
    {
        if($this->validarUsuario('IntranetPresolicitud')){

            $data = IntranetPresolicitudController::apiEliminar($id);
            if(!$data){
                return response()->json(['error'=>'informacion defectuosa'],422);
            }
            return response()->json(['data'=>$data],200);
        }else {

            return response()->json(['error'=>'permiso denegado'],400);
        }
    }

}


