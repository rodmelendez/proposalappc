<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class EventDetails extends Model
{
  protected $table = "event_details";

  protected $fillable = ['description','type','amount','start_date','due_date'];
  public function validate($data)
  {
      $rules = [
          'id_event'=>'integer',
          'description'=>'max:60',
          'amount'=>'numeric',
          'start_date'=>'date',
          'due_date'=>'date',
      ];

      $validate = Validator::make($data, $rules);
      if($validate->fails()){
          return false ;
      }
      return true;
  }
  protected $hidden = ['created_at','updated_at'];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */

}
