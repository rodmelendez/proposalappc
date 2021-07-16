<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 13/7/2019
 * Time: 3:35 PM
 */

namespace App\Http\Controllers;


use App\HorarioPlantilla;
use Illuminate\Support\Facades\Input;

class HorarioPlantillaController extends Controlador {

    protected $modelo = 'HorarioPlantilla';


    public function guardarEventosPost() {
        $id_horario_plantilla = (int)Input::get('id_horario_plantilla');
        $items = json_decode(Input::get('items'));

        if (!($horario_plantilla = HorarioPlantilla::find($id_horario_plantilla))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        if (!is_array($items)) {
            return $this->retornarError();
        }

        $horario_plantilla->guardarHorario($items);

        return $this->retornar(self::MSJ_GUARDADO);
    }


    public function cargarHorarioGet() {
        $id_horario_plantilla = (int)Input::get('id_horario_plantilla');

        if (!($horario_plantilla = HorarioPlantilla::find($id_horario_plantilla))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $items = [];
        $dias = $horario_plantilla->dias()->get();

        foreach ($dias as $dia) {
            $items[] = [
                'dia' => $dia->dia,
                'hora_inicio' => $dia->hora_inicio,
                'hora_fin' => $dia->hora_fin,
            ];
        }

        $this->especificarRespuesta('items', $items);
        return $this->retornar();
    }

}