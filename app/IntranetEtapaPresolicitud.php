<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetEtapaPresolicitud extends \App\Modelo
{
  protected $table = 'intranet_etapa_presolicitud';
  protected $fillable = [
        'id_presolicitud',
        'etapa',
        'primerChek',
        'segundoChek',
        'duracion',
        'fecha_registro',
        'estatus'
    ];
  protected $hidden = ['created_at','updated_at'];

   /**
   * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto
   *
   * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
   * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
   * @return array|string
   */
  public static function reglasValidacion($campo = null, $ignorar_id = 0) {
      $reglas = [
        'id_presolicitud'=>'integer',
        'etapa'=>'integer',
        'duracion'=>'numeric',
        'fecha_registro'=>'date',
      ];
      if ($campo === null) {
          return $reglas;
      }
      return isset($reglas[$campo]) ? $reglas[$campo] : '';
  }
  public static function traerData() {
      $campos = [
        'id_presolicitud',
        'etapa',
        'primerChek',
        'segundoChek',
        'duracion',
        'fecha_registro',
        'estatus'
      ];

      return self::orderBy('fecha_registro')
          ->get($campos)
          ->toArray();
  }
  public function presolicitud() {
    return $this->belongsTo('App\IntranetPresolicitud', 'id_presolicitud', 'id');
  }
  public function movimientos() {
  return $this->hasMany('App\IntranetMovimientoPresolicitud', 'id_etapa_presolicitud', 'id');
  }
}
