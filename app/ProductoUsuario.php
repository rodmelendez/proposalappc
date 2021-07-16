<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoUsuario extends Model {

    public $timestamps = false;

    protected $table = 'producto_usuario';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_producto',
        'id_usuario',
        'id_empleado',
        'id_persona',
        'fecha_creacion',
    ];


    # RELACIONES


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

}