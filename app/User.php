<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'usuario';

    const CREATED_AT = 'fecha_creacion';

    const UPDATED_AT = 'fecha_actualizacion';

    const DELETED_AT = 'fecha_eliminacion';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'contrasena',
        'admin',
    ];


    public $booleanos = [
        'admin',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'contrasena',
        'remember_token',
        'api_token',
        'pivot',
    ];


    const STATUS_HABILITADO = 1;
    const STATUS_INHABILITADO = 2;


    /**
     * Devuélve los mensajes de validación para un campo específico o el arreglo de mensajes por defecto
     * Los mensajes se buscan en el archivo de correspondiente en lang
     *
     * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
     * @return array|string
     */
    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'id'                    => 'integer',
            'nombre'                => 'required|max:60|unique:usuario,nombre,' . $ignorar_id . ',id,fecha_eliminacion,NULL',
            'contrasena'            => 'max:70',
            'contrasena_confirmar'  => 'same:contrasena',
            'admin'                 => 'nullable|in:0,1,on',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->contrasena;
    }


    public function setContrasenaAttribute($value) {
        if (!empty($value)) {
            if (Hash::needsRehash($value)) {
                $value = Hash::make($value);
            }
            $this->attributes['contrasena'] = $value;
        }
    }


    public function setAdminAttribute($value) {
        //solo un administrador de sistema puede modificar este atributo
        $es_admnin = Auth::user()->admin;
        if ($es_admnin) {
            $this->attributes['admin'] = $value;
        }
    }


    # RELACIONES

    /**
     * La relación está estructurada de muchos a muchos
     * pero se va a usar como uno a uno con tabla pivote
     */
    public function persona() {
        return $this->belongsToMany('App\Persona', 'usuario_persona', 'id_usuario', 'id_persona');
    }


    public function rol() {
        return $this->belongsToMany('App\Rol', 'usuario_rol', 'id_usuario', 'id_rol');
    }


    public function permisos() {
        return $this->belongsToMany('App\Permiso', 'usuario_permiso', 'id_usuario', 'id_permiso');
    }


    # METODOS

    /**
     * Registra una nueva sesión para el usuario
     *
     * @return bool
     */
    public function iniciarSesion() {
        Auth::login($this);
        if (Auth::check()) {
            $this->fecha_ultimo_ingreso = date('Y-m-d H:i:s');
            $this->save();
            return true;
        }
        return false;
    }


    /**
     * Busca y retorna la persona asociada al usuario
     *
     * @return Persona
     */
    public function traerPersona() {
        return $this->persona()->first();
    }


    /**
     * Proceso personalizado para eliminar un registro
     */
    public function eliminar() {
        //$this->delete();
        $campo_fecha_eliminacion = self::DELETED_AT;
        $this->$campo_fecha_eliminacion = date('Y-m-d H:i:s');
        $this->status = 0;
        $this->save();
    }


    public static function traerData($campos = null) {
        //MySQL = GROUP_CONCATENATE
        //POSTGRES = STRING_AGG

        $tipo_telefono = Contacto::TIPO_TELEFONO;
        $tipo_correo = Contacto::TIPO_CORREO;

        $campo_persona = <<<EOT
        (
            SELECT STRING_AGG(COALESCE(persona.primer_nombre,'') || ' ' || COALESCE(persona.primer_apellido,'') || ' (' || COALESCE(persona.dni,'') || ')', ', ') 
            FROM persona 
            LEFT JOIN usuario_persona ON usuario_persona.id_persona = persona.id 
            WHERE usuario_persona.id_usuario = usuario.id
        ) AS nombre_persona
EOT;

        $campo_foto = <<<EOT
        (
            SELECT persona.foto 
            FROM persona 
            LEFT JOIN usuario_persona ON usuario_persona.id_persona = persona.id 
            WHERE usuario_persona.id_usuario = usuario.id
        ) AS foto
EOT;

        $campo_telefono = <<<EOT
        (
            SELECT STRING_AGG(contacto.valor, ', ') 
            FROM persona 
            LEFT JOIN usuario_persona ON usuario_persona.id_persona = persona.id 
            LEFT JOIN contacto ON contacto.id_persona = persona.id 
            WHERE usuario_persona.id_usuario = usuario.id 
                AND contacto.tipo = {$tipo_telefono}
        ) AS telefono
EOT;

        $campo_correo = <<<EOT
        (
            SELECT STRING_AGG(contacto.valor, ', ') 
            FROM persona 
            LEFT JOIN usuario_persona ON usuario_persona.id_persona = persona.id 
            LEFT JOIN contacto ON contacto.id_persona = persona.id 
            WHERE usuario_persona.id_usuario = usuario.id 
                AND contacto.tipo = {$tipo_correo}
        ) AS correo
EOT;

        $campo_rol = <<<EOT
        (
            SELECT STRING_AGG(rol.nombre, '|') 
            FROM rol 
            LEFT JOIN usuario_rol ON usuario_rol.id_rol = rol.id 
            WHERE usuario_rol.id_usuario = usuario.id
        ) AS rol
EOT;

        $campos = [
            'usuario.id',
            'usuario.nombre',
            $campo_persona,
            $campo_rol,
            $campo_telefono,
            $campo_correo,
            $campo_foto,
            'empleado.num_control',
            'empleado.fecha_ingreso',
            'empleado.tipo_cargo',
            'empleado.id_empresa',
            'empleado.id_sucursal',
            'empleado.id_departamento',
            'usuario.admin',
            'usuario.fecha_creacion',
            'usuario.status',
            'empresa.nombre AS empresa',
        ];

        $items = self::orderBy('nombre');

        Modelo::verificarPermiso($items, 'usuarios', 'usuario');

        return $items
            ->join('usuario_persona', 'usuario_persona.id_usuario', '=', 'usuario.id', 'left')
            ->join('persona', 'usuario_persona.id_persona', '=', 'persona.id', 'left')
            ->join('empleado', 'persona.id', '=', 'empleado.id_persona', 'left')
            ->join('empresa', 'empleado.id_empresa', '=', 'empresa.id', 'left')
            ->where('usuario.nombre', '<>', 'admin')
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


    public function traerPermisos() {
        $permisos = $this->permisos()->selectRaw('usuario_permiso.id_permiso AS id, usuario_permiso.valor AS valor')->get();

        $resultado = [];

        foreach ($permisos as $permiso) {
            $resultado[] = [
                'id' => $permiso->id,
                'valor' => $permiso->valor
            ];
        }

        return $resultado;
    }


    /**
     * Asigna los permisos de un Rol a el usuario
     *
     * @param $rol
     */
    public function actualizarPermisosDesdeRol($rol) {
        if ($rol) {
            $permisos = $rol->permisos()->selectRaw('rol_permiso.id_permiso AS id, rol_permiso.valor AS valor')->get();
            $permisos_usuario = [];

            foreach ($permisos as $permiso) {
                $permisos_usuario[$permiso->id] = [
                    'valor' => $permiso->valor
                ];
            }

            $this->permisos()->sync($permisos_usuario);
        }
    }


    /**
     * Retorna el nombre del rol asociado al usuario
     *
     * @return string
     */
    public function nombreRol() {
        if ($this->admin) {
            return __('user.administrador');
        }

        $rol = $this->rol()->first(['rol.nombre']);
        if ($rol) {
            return $rol->nombre;
        }

        return '';
    }


    /**
     * Busca en los permisos del usuario y retorna verdadero si tiene el permiso especificado
     *
     * @param $nombre_permiso
     * @param null $categoria
     * @return bool
     */
    public function puede($nombre_permiso, $categoria = null) {
        //administrador tiene todos los permisos
        if ($this->admin) return true;

        //si las acciones son en el usuario del usuario actual
        //if (strtolower($categoria) == 'user' && $this->id == $id_objetivo) return true;

        $permisos = $this->permisos()->where('permiso.nombre', '=', $nombre_permiso);
        if (!empty($categoria)) {
            $permisos = $permisos->where('permiso.categoria', '=', $categoria);
        }
        return $permisos->count() > 0;
    }


    /**
     * Retorna verdadero si el usuario actual es administrador de sistema
     *
     * @return mixed
     */
    public static function esAdmin() {
        $usuario = Auth::user();
        return (bool)$usuario->admin;
    }


    /**
     * Busca en los permisos del usuario actual y retorna verdadero si tiene el permiso especificado
     *
     * @param $nombre_permiso
     * @param $categoria
     * @param null $usuario
     * @return bool
     */
    public static function tienePermiso($nombre_permiso, $categoria = null, $usuario = null) {
        if ($usuario === null) $usuario = Auth::user();

        return $usuario ? $usuario->puede($nombre_permiso, $categoria) : false;
    }


    public function guardarPermisos($permisos) {
        $ids_permisos = [];

        foreach ($permisos as $permiso) {
            if (is_int($permiso)) {
                $ids_permisos[] = $permiso;
            }
            else {
                $cat_accion = explode('|', $permiso);
                if (count($cat_accion) == 2) {
                    $categoria = $cat_accion[0];
                    $accion = $cat_accion[1];
                }
                else {
                    $categoria = $permiso;
                    $accion = 'admin';
                }

                $permiso_obj = Permiso::where('categoria', '=', $categoria)
                    ->where('nombre', '=', $accion)
                    ->first();

                if (!$permiso_obj) {
                    $permiso_obj = Permiso::create([
                        'categoria' => $categoria,
                        'nombre' => $accion,
                    ]);

                    if (!$permiso_obj) continue;
                }

                $ids_permisos[] = $permiso_obj->id;
            }
        }

        $this->permisos()->sync($ids_permisos);
    }


    public function alternarHabilitar() {
        $this->status = $this->status == self::STATUS_INHABILITADO ? self::STATUS_HABILITADO : self::STATUS_INHABILITADO;
        $this->save();
    }
}
