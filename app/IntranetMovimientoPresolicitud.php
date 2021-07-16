<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetMovimientoPresolicitud extends \App\Modelo
{
  protected $table = 'intranet_movimiento_presolicitud';
  protected $fillable = [
                  'id_usuario',
                  'id_etapa_presolicitud',               
                  'descripcion',
                  'fecha',
                  'movimiento'
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
          'descripcion'=> 'max:255',
          'etapa'=> 'integer',
          'fecha'=> 'date',
          'id_usuario'=> 'integer',
          'id_etapa_presolicitud'=> 'integer',
          'movimiento'=>'max:63'

      ];
      if ($campo === null) {
          return $reglas;
      }
      return isset($reglas[$campo]) ? $reglas[$campo] : '';
  }
  public static function traerData() {
      $campos = [
        'id_usuario',
        'id_presolicitud',
        'descripcion',
        'fecha',
        'movimiento'
      ];

      return self::orderBy('fecha')
          ->get($campos)
          ->toArray();
  }
  
  public function usuario() {
  return $this->belongsTo('App\User', 'id_usuario', 'id');
  }
}
