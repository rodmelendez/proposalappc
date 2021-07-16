<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 11/6/2019
 * Time: 10:30 AM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComunicadoDestinatario extends Model {

    public $timestamps = false;

    protected $table = 'comunicado_destinatario';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_comunicado',
        'tio',
        'valor',
        'valor_str',
    ];

    //definiciones de tipos
    const TIPO_EMPRESA = 1;
    const TIPO_USUARIO = 2;


    # RELACIONES


    # FILTROS

    public function scopeTipoEmpresa($query) {
        return $query->where('tipo', '=', self::TIPO_EMPRESA);
    }


    # ASIGNACIONES


    # LECTURAS


    # METODOS

}