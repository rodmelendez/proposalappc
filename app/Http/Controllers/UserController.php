<?php
namespace App\Http\Controllers;


use App\Empleado;
use App\EstudianteExamen;
use App\EstudiantePago;
use App\Examen;
use App\Libro;
use App\MatriculaGrupo;
use App\Opcion;
use App\Permiso;
use App\Persona;
use App\Profesor;
use App\Rol;
use App\Salon;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class UserController extends Controlador {

    protected $modelo = 'User';


    public static function cabeceras() {
        return [
            'id',
            'nombre',
            'nombre_persona',
            'rol',
            'admin',
            'fecha_creacion'
        ];
    }


    public static function configuracionColumnasDataTables() {
        return [
            'nombre'            => 'negritas',
            'nombre_persona'    => 'titulo',
            'rol'               => 'etiquetas',
            'admin'             => 'check',
            'fecha_creacion'    => 'fecha_hora'
        ];
    }


    public function tablaBusqueda() {
        return 'vw_usuario';
    }


    public function camposBusqueda() {
        return [
            'nombre',
            'primer_nombre',
            'segundo_nombre',
            'primer_apellido',
            'segundo_apellido',
            'dni',
        ];
    }


    public function admin() {
        if (!User::tienePermiso('consultar', 'User')) {
            return $this->sinPermiso();
        }

        $roles = Rol::pluck('nombre', 'id')->toArray();

        $permisos = Permiso::orderBy('categoria')->orderBy('nombre')->get();

        $secciones = [];
        $campos = [];
        foreach ($permisos as $permiso) {
            $titulo_categoria = __(strtolower($permiso->categoria) . '.titulo_plural');
            if (!isset($secciones[$permiso->categoria])) {
                $secciones[$permiso->categoria] = [
                    'nombre' => $permiso->categoria,
                    'titulo' => $titulo_categoria
                ];
                $campos[$permiso->categoria] = [];
            }

            $campo = new stdClass();
            $campo->id = $permiso->id;
            $campo->nombre = $permiso->nombre;
            if ($campo->nombre != '*') {
                $campo->descripcion = __('rol.' . $permiso->nombre);
                $campo->tipo = Permiso::TIPO_BOOLEANO;
            }
            else {
                $campo->descripcion = $titulo_categoria;
                $campo->tipo = Permiso::TIPO_SELECCION;
                //items del modelo
                $modelo = '\\App\\' . Str::title($permiso->categoria);
                $campo->opciones = $modelo::pluck('nombre', 'id')->toArray();
            }

            $campos[$permiso->categoria][] = $campo;
        }

        return view('user')->with([
            'fuente' => 'user',
            'roles' => $roles,
            'secciones' => $secciones,
            'campos' => $campos
        ]);
    }


    /**
     * Retorna el arreglo de resultados para el select2 incluyendo la foto
     *
     * @param $items
     * @return array
     */
    public function listadoResultado($items) {
        $avatar_defecto = asset('img/avatar-defecto.jpg');

        $resultado = [];
        foreach ($items as $item) {
            $resultado[] = [
                'id' => $item->id,
                'text' => empty($item->primer_nombre) ? $item->nombre : ucwords(mb_strtolower($item->primer_nombre . ' ' . $item->primer_apellido, 'UTF-8')), //TODO: ucwords no funciona con acentos; nombres con acentos en la primera letra no se van a pasar a mayúsculas
                'subtext' => $item->nombre . (!empty($item->rol) ? (' (' . $item->rol . ')') : ''),
                'img' => PersonaController::urlFoto($item->foto, 's', $avatar_defecto)
            ];
        }
        return $resultado;
    }


    public function antesDeValidar() {
        $rol = Input::get('rol');

        if ($rol == 'admin') {
            Input::merge([
                'admin' => true,
                'rol' => null,
            ]);
        }

        return true;
    }


    /**
     * Antes de guardar, se verifican los datos enviados de la persona
     */
    public function antesDeGuardar() {
        $this->antesDeGuardarDefecto();

        $id = (int)Input::get('id');

        //cuando se está editanto un usuario
        if ($id) {
            if (!$this->verificarInputsDatosPersonaUsuarioExistente($id)) {
                return false;
            }
        }

        //cuando se está creando un usuario
        else {
            if (!$this->verificarInputsDatosPersonaUsuarioNuevo()) {
                return false;
            }

            //si no hay errores de validación, se guarda la persona
            //cuando se está registrando una persona nueva

            $persona_existente = Input::get('persona_existente', false);

            if (!$persona_existente) {
                if (!$this->crearPersonaDesdeInputs()) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Después de guardar el usuario se guarda el rol seleccionado, y los datos de la persona
     *
     * @param $item
     */
    public function despuesDeGuardar($item) {
        $id = (int)Input::get('id');
        $id_rol = (int)Input::get('id_rol');
        $persona_existente = Input::get('persona_existente', false);
        $id_persona = (int)Input::get('id_persona', 0);

        //rol
        if ($id_rol) {
            $item->rol()->sync([$id_rol]);
        } else {
            $item->rol()->sync([]);
        }

        //permisos
        /*if (!$id && $id_rol) {
            $rol = Rol::find($id_rol);
            $item->actualizarPermisosDesdeRol($rol);
        }*/

        //datos de la persona
        if ($id && !$persona_existente && $id_persona) {
            $this->actualizarPersonaDesdeInputs($id_persona);
        }

        //asocia la persona con el usuario
        $item->persona()->sync([$id_persona]);

        //datos del empleado
        $this->actualizarEmpleadoDesdeInputs($id_persona);
    }


    /**
     * Cuando se solicitan los datos del estudiante para editar se
     * envían también los datos de la persona
     *
     * @param $item
     */
    public function itemDataAdicional($item) {
        //persona
        $persona = $item->persona()->first();

        if ($persona) {
            $data = $persona->toArray();
            $data['id_persona'] = $data['id'];

            $this->agregarArregloEnRespuesta($data);

            //contacto
            $this->especificarRespuesta('telefonos', $persona->telefonos());
            $this->especificarRespuesta('correos', $persona->correos());
        }

        //empleado
        if ($persona) {
            $empleado = Empleado::where('id_persona', '=', (int)$persona->id)->first();

            if ($empleado) {
                $this->especificarRespuesta('empleado', $empleado->toArray());

                //empresas
                $empresas = $empleado->empresas()->get()->pluck('id')->toArray();
                $this->especificarRespuesta('empresas', $empresas);
            }
        }

        //rol
        $rol = $item->rol()->first();
        $this->especificarRespuesta('id_rol', $rol ? $rol->id : null);
    }


    /**
     * Verifica los datos de la persona cuando se está editando el usuario
     *
     * @param $id
     * @return bool
     */
    public function verificarInputsDatosPersonaUsuarioExistente($id) {
        //busca el usuario
        $usuario = User::find($id);

        if ($usuario) {
            $persona_existente = Input::get('persona_existente', false);

            $persona = $usuario->persona()->first();
            if (!$persona) {
                $this->retornarError(__('persona.registro_no_existente'));
                return false;
            }

            //cuando se está asignando una persona existente
            if ($persona_existente) {
                if (!$this->verificarInputsPersonaAsignada($persona ? $persona->id : 0)) {
                    return false;
                }
            }

            //cuando se está editando una persona
            else {
                if (!$this->verificarInputsPersonaNueva($persona ? $persona->id : 0)) {
                    return false;
                }

                Input::merge(['id_persona' => $persona->id]);
            }
        }
        else {
            $this->retornarError(self::ERROR_NO_ENCONTRADO);
            return false;
        }

        return true;
    }


    /**
     * Verifica los datos de la persona cuando se está creando un usuario
     *
     * @return bool
     */
    public function verificarInputsDatosPersonaUsuarioNuevo() {
        $persona_existente = Input::get('persona_existente', false);

        //cuando se está asignando una persona existente
        if ($persona_existente) {
            if (!$this->verificarInputsPersonaAsignada()) {
                return false;
            }
        }

        //cuando se está creando una persona nueva
        else {
            if (!$this->verificarInputsPersonaNueva()) {
                return false;
            }
        }

        return true;
    }


    /**
     * Verifica que los datos enviados de la persona sean válidos
     *
     * @param int $id_persona_a_ignorar
     * @return bool
     */
    public function verificarInputsPersonaNueva($id_persona_a_ignorar = 0) {
        //reglas de validación de persona
        $reglas = Persona::reglasValidacion(null, $id_persona_a_ignorar);

        //valida datos de la persona
        $validacion = Validator::make(Input::all(), $reglas);

        if (!$validacion->passes()) {
            list($err, $campo) = $this->mensajeYCampoDeError($validacion);
            $this->retornarError($err, $campo);
            return false;
        }

        return true;
    }


    /**
     * Verifica que la persona enviada a asignar sea válida
     *
     * @param int $id_persona_a_ignorar
     * @return bool
     */
    public function verificarInputsPersonaAsignada($id_persona_a_ignorar = 0) {
        $id_persona = (int)Input::get('id_persona');

        //busca la persona
        $persona = Persona::find($id_persona);

        if ($persona) {
            if ($id_persona != $id_persona_a_ignorar) {

                //si la persona ya está asignada a un usuario, retorna un error
                if ($persona->idUsuario()) {
                    $this->retornarError(__('user.persona_ya_asignada'), 'id_persona');
                    return false;
                }

            }
        }
        else {
            $this->retornarError(self::ERROR_NO_ENCONTRADO);
            return false;
        }

        return true;
    }


    /**
     * Crea la persona y guarda el id. Los datos ya deben estar validados
     *
     * @return bool
     */
    public function crearPersonaDesdeInputs() {
        $persona = Persona::create(Input::all());
        if ($persona && $persona->id) {
            //registra el id de la persona creada para ser asignada al usuario
            Input::merge([
                'id_persona' => $persona->id
            ]);

            $ctrl = new PersonaController;
            $ctrl->despuesDeGuardar($persona);
        }
        else {
            $this->retornarError(__('global.unable_perform_action'));
            return false;
        }
        return true;
    }


    /**
     * Actualiza la persona. Los datos ya deben estar validados
     *
     * @param $id
     */
    public function actualizarPersonaDesdeInputs($id) {
        $persona = Persona::find((int)$id);
        if ($persona) {
            $persona->update(Input::all());

            $ctrl = new PersonaController;
            $ctrl->despuesDeGuardar($persona);
        }
    }


    public function actualizarEmpleadoDesdeInputs($id_persona) {
        if (!$id_persona) return;

        $empleado = Empleado::where('id_persona', '=', (int)$id_persona)->first();

        $id_empresa = null;
        $empresas = Input::get('empresa');
        $id_sucursal = Input::get('id_sucursal', null);
        $id_departamento = Input::get('id_departamento', null);
        $id_sub_departamento = Input::get('id_sub_departamento', null);
        $lista_empresas = [];

        if (is_array($empresas)) {
            foreach ($empresas as $empresa) {
                if (!$id_empresa) $id_empresa = $empresa;

                $lista_empresas[$empresa] = [
                    'id_sucursal' => $id_sucursal,
                    'id_departamento' => $id_departamento,
                    'id_sub_departamento' => $id_sub_departamento,
                ];
            }
        }

        $data = [
            'id_persona' => (int)$id_persona,
            'num_control' => Input::get('num_control'),
            'fecha_ingreso' => Input::get('fecha_ingreso'),
            'descripcion' => Input::get('descripcion'),
            'grado' => Input::get('grado'),
            'tipo_cargo' => Input::get('tipo_cargo'),
            'salario_actual' => Input::get('salario_actual'),
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'id_departamento' => $id_departamento,
            'id_sub_departamento' => $id_sub_departamento,
        ];

        if (!$empleado) {
            $empleado = Empleado::create($data);
        }
        else {
            foreach ($data as $prop => $valor) {
                $empleado->$prop = $valor;
            }
            $empleado->save();
        }

        $empleado->empresas()->sync($lista_empresas);
    }


    /**
     * Retorna un JSON con un arreglo con los permisos de un usuario específico según el id
     *
     * @return array
     */
    public function cargarPermisosGetDEPRECATED() {
        $id = Input::get('id');
        if ($id) {
            $usuario = User::find((int)$id);
            if ($usuario) {
                //$permisos = $usuario->permisos->pluck('id')->toArray();
                $permisos = $usuario->traerPermisos();

                $this->especificarRespuesta('permisos', $permisos);
                $this->especificarRespuesta('usuario', $usuario->nombre);
                $this->especificarRespuesta('titulo', __('user.titulo') . ' "' . $usuario->nombre . '"');
                $this->especificarRespuesta('id_usuario', $usuario->id);
                $this->especificarRespuesta('es_administrador', (bool)$usuario->admin);
                return $this->retornar();
            }

            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        return $this->retornarError();
    }


    public function cargarPermisosGet() {
        $id_usuario = (int)Input::get('id');

        if (!($usuario = User::find($id_usuario))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $lista = [];

        //retorna los permisos asociados al usuario
        $permisos = $usuario->permisos()->get();

        foreach ($permisos as $permiso) {
            if (!isset($lista[$permiso->categoria])) {
                $lista[$permiso->categoria] = [
                    'id' => $permiso->id,
                    'nombre' => $permiso->nombre,
                    'categoria' => $permiso->categoria,
                    'acciones' => [
                        $permiso->nombre
                    ]
                ];
            }
            else {
                $lista[$permiso->categoria]['acciones'][] = $permiso->nombre;
            }
        }

        $this->especificarRespuesta('permisos', array_values($lista));
        $this->especificarRespuesta('admin', $usuario->admin);
        return $this->retornar();
    }


    /**
     * Guarda los permisos del rol desde el formulario
     *
     * @return array
     */
    public function guardarPermisosPostDEPRECATED() {
        $id = Input::get('id');
        if ($id) {
            $usuario = User::find((int)$id);
            if ($usuario) {
                $permisos = json_decode(Input::get('permisos', ''));

                $checks = [];

                if (is_array($permisos)) {
                    foreach ($permisos as $permiso) {
                        if (is_int($permiso)) {
                            $checks[$permiso] = ['valor' => null];
                        }
                        elseif (is_object($permiso)) {
                            $checks[$permiso->id] = ['valor' => implode(',', $permiso->valor)];
                        }
                    }
                }

                $usuario->permisos()->sync($checks);

                return $this->retornar(__('global.saved_msg'));
            }
            else {
                return $this->retornarError(self::ERROR_NO_ENCONTRADO);
            }
        }

        return $this->retornarError();
    }


    public function guardarPermisosPost() {
        $id_usuario = (int)Input::get('id');
        $permisos = Input::get('permisos');

        if (!is_array($permisos)) {
            return $this->retornarError();
        }

        if (!($usuario = User::find($id_usuario))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $usuario->guardarPermisos($permisos);

        return $this->retornar(self::MSJ_GUARDADO);
    }


    /**
     * Procesa la solicitud para cambiar la contraseña
     *
     * @return mixed
     */
    public function cambiarContrasenaPost() {
        //$id_usuario = (int)Input::get('id_usuario');

        //if ($usuario = User::find($id_usuario)) {
            $usuario = $usuario_actual = Auth::user();

            $contrasena = Input::get('contrasena_nueva');
            $contrasena_confirmar = Input::get('contrasena_nueva_confirmar');
            $contrasena_actual = Input::get('contrasena_actual');

            if (empty($contrasena)) {
                return $this->retornarError(__('user.contrasena_vacia'));
            }

            if ($contrasena != $contrasena_confirmar) {
                return $this->retornarError(__('user.contrasena_confirmar_no_coincide'));
            }

            //verifica que tenga permisos o que sea la misma cuenta del usuario que ha iniciado sesión

            //$puede_cambiar_contrasenas = $usuario_actual->puede('cambiar_contrasenas');

            /*if (!$puede_cambiar_contrasenas && $usuario_actual->id != $usuario->id) {
                return $this->retornarError(self::ERROR_PERMISO_DENEGADO);
            }*/

            //if (!$puede_cambiar_contrasenas) {
                if (!Hash::check($contrasena_actual, $usuario->contrasena)) {
                    return $this->retornarError(__('user.contrasena_actual_incorrecta'));
                }
            //}

            //se actualiza la contrasena
            $usuario->contrasena = $contrasena;
            $usuario->save();

            return $this->retornar(__('user.contrasena_actualizada'));
        //}

        //return $this->retornarError(self::ERROR_NO_ENCONTRADO);
    }


    public function personalizarPost() {
        $opciones = [
            'color_fondo' => Input::get('color_fondo', ''),
        ];

        Opcion::guardarArreglo($opciones, true);

        $this->especificarRespuesta('opciones', $opciones);
        return $this->retornar(self::MSJ_GUARDADO);
    }


    public function cargarDataGet() {
        $opciones = Opcion::valores();

        $this->agregarArregloEnRespuesta($opciones);
        return $this->retornar();
    }


    public function indexGet() {
        return User::traerData();
    }


    public function loggedUserGet() {
        $usuario = Auth::user();

        if (!$usuario) {
            return $this->retornarError('No se ha iniciado sesión');
        }

        if ($persona = $usuario->persona()->first()) {
            $this->especificarRespuesta('persona', $persona);

            if ($empleado = $persona->empleado()) {
                $this->especificarRespuesta('empleado', $empleado);

                $empresas = $empleado->empresas()->get()->toArray();
                $this->especificarRespuesta('empresas', $empresas);
            }
        }


        $this->agregarArregloEnRespuesta($usuario->toArray());

        return $this->retornar();
    }


    public function cargarAvatarGet() {
        $nombre_usuario = Input::get('nombre_usuario');

        $nombre_img = '';
        $color_fondo = '';

        if (!empty($nombre_usuario)) {
            if ($usuario = User::where('nombre', '=', $nombre_usuario)->first()) {
                if ($persona = $usuario->traerPersona()) {
                    $nombre_img = $persona->foto;
                }

                $color_fondo = Opcion::valor('color_fondo', '', $usuario->id);
            }
        }

        $this->especificarRespuesta('avatar', PersonaController::urlFoto($nombre_img));
        $this->especificarRespuesta('color_fondo', $color_fondo);
        return $this->retornar();
    }


    public function codigoQrGet() {
        $id_usuario = (int)Input::get('id');

        if (!($usuario = User::find($id_usuario))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        if (!($persona = $usuario->traerPersona())) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        if (!($empleado = $persona->empleado())) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $codigo_qr = EmpleadoController::codigoQr($empleado, $persona);

        $this->especificarRespuesta('codigo_qr', $codigo_qr);
        return $this->retornar();
    }

}