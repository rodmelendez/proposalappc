<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 28/11/2017
 * Time: 11:41 AM
 */

namespace App;

class Rol extends Modelo {

    public $timestamps = true;

    protected $table = 'rol';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion'
    ];


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
            'nombre'        => 'required|max:40|unique:rol,nombre,' . $ignorar_id . ',id,fecha_eliminacion,NULL',
            'descripcion'   => 'max:255'
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    /**
     * Devuélve los mensajes para un campo específico o el arreglo de mensajes por defecto
     *
     * @param $campo
     * @return array|string
     */
    public static function mensajesValidacion($campo = null) {
        $mensajes = [

        ];
        if ($campo === null) {
            return $mensajes;
        }
        return isset($mensajes[$campo]) ? $mensajes[$campo] : '';
    }


    # RELACIONES

    public function permisos() {
        return $this->belongsToMany('App\Permiso', 'rol_permiso', 'id_rol', 'id_permiso');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS

    public function getNombreAttribute() {
        return ucwords($this->attributes['nombre']);
    }

    public function getDescripcionAttribute() {
        return ucfirst(mb_strtolower($this->attributes['descripcion'], 'UTF-8'));
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'nombre',
            'descripcion',
            'fecha_creacion'
        ];
        return self::orderBy('nombre')
            ->get($campos)
            ->toArray();
    }


    public function traerPermisos() {
        $permisos = $this->permisos()->selectRaw('rol_permiso.id_permiso AS id, rol_permiso.valor AS valor')->get();

        $resultado = [];

        foreach ($permisos as $permiso) {
            $resultado[] = [
                'id' => $permiso->id,
                'valor' => $permiso->valor
            ];
        }

        return $resultado;
    }


    public function actualizarPermisosUsuarios($checks = null) {
        if ($checks === null) {
            $permisos = $this->permisos()->get(['id', 'valor']);

            $checks = [];
            foreach ($permisos as $permiso) {
                $checks[$permiso->id] = ['valor' => $permiso->valor];
            }
        }

        $usuarios = User::join('usuario_rol', 'usuario_rol.id_usuario', '=', 'usuario.id')
            ->where('id_rol', '=', (int)$this->id)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->permisos()->sync($checks);
        }
    }

}