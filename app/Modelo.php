<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 20/9/2017
 * Time: 5:23 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Modelo extends Model {

    use SoftDeletes;

    const CREATED_AT = 'fecha_creacion';

    const UPDATED_AT = 'fecha_actualizacion';

    const DELETED_AT = 'fecha_eliminacion';

    public $booleanos = [];

    protected $hidden = [
        'pivot',
    ];

    /**
     * Retorna los datos que se van a cargar en el DataTables
     *
     * @return array
     */
    public static function traerData() {

        //var_dump(self::get()); dd();
        return self::get()->toArray();
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


    /**
     * Scope (filtro) para ignorar un id (en casos donde se requiere un valor único)
     *
     * @param $query
     * @param $val
     * @return mixed
     */
    public function scopeIgnorarId($query, $val) {
        if (!empty($val)) {
            return $query->where('id', '<>', (int)$val);
        }
        return $query;
    }


    public static function verificarPermiso(&$q, $categoria, $tabla = '', $usuario = null) {
        if ($usuario === null) {
            $usuario = Auth::user();
        }

        if ($usuario->admin) return;

        $tabla = $tabla ? ($tabla . '.') : '';

        if (!$usuario) {
            $q->where($tabla . 'id', '=', 0); //nada
            return;
        }

        if ($usuario->puede('consultar', $categoria)) {
            if (!$usuario->puede('todos', $categoria)) {
                $q->where($tabla . 'id_usuario', '=',  $usuario->id);
            }
        }
        else {
            $q->where($tabla . 'id', '=', 0); //nada
        }
    }

}