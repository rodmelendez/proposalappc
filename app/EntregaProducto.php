<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class EntregaProducto extends Model {

    public $timestamps = true;

    protected $table = 'entrega_producto';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_entrega',
        'id_producto',
        'tipo',
        'modelo',
        'marca',
        'atributos',
    ];


    # RELACIONES


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

}