<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 17/7/2019
 * Time: 1:10 PM
 */

namespace App\Http\Controllers;


use App\Empleado;
use App\Funciones;
use App\Horario;
use App\HorarioEvento;
use Illuminate\Support\Facades\Input;

class HorarioController extends Controlador {

    protected $modelo = 'Horario';


    public function cargarHorarioEmpleadoGet() {
        $id_empleado = (int)Input::get('id_empleado');
        $fecha_inicio = Input::get('fecha_inicio');
        $fecha_fin = Input::get('fecha_fin');

        if (!($empleado = Empleado::find($id_empleado))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $eventos = $empleado->eventos($fecha_inicio, $fecha_fin);

        /*$eventos = [];

        $eventos[] = [
            'id' => 1,
            'inicio' => $fecha_inicio . ' 09:' . (10 + rand(0,49)) . ':00',
            'fin' => $fecha_inicio . ' ' . (10 + rand(0,13)) . ':45:00',
        ];*/

        $this->especificarRespuesta('eventos', $eventos);
        return $this->retornar();
    }


    public function asignarNuevoHorarioPost() {
        $id_empleado = (int)Input::get('id_empleado');
        $fecha = Input::get('fecha');
        $hora_inicio = Input::get('hora_inicio');
        $hora_fin = Input::get('hora_fin');

        if (empty($id_empleado) || empty($fecha) || empty($hora_inicio) || empty($hora_fin)) {
            return $this->retornarError();
        }

        if (!($empleado = Empleado::find($id_empleado))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $empleado->asignarHorario($fecha, $hora_inicio, $hora_fin);

        return $this->retornar(self::MSJ_GUARDADO);
    }


    public function actualizarHorarioItemPost() {
        $id_empleado = (int)Input::get('id_empleado');
        $id = (int)Input::get('id');
        $inicio = Input::get('inicio');
        $fin = Input::get('fin');

        if (empty($id_empleado) || empty($id) || empty($inicio) || empty($fin)) {
            return $this->retornarError();
        }

        if (!($horario = Horario::find($id))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        if ($horario->id_empleado != $id_empleado) {
            return $this->retornarError(self::ERROR_PERMISO_DENEGADO);
        }

        $horario->actualizarItem($inicio, $fin);

        return $this->retornar();
    }


    public function asignarPlantillaPost() {
        $id_empleado = (int)Input::get('id_empleado');
        $id_horario_plantilla = (int)Input::get('id_horario_plantilla');
        $fecha_inicio = Input::get('fecha_inicio');
        $fecha_fin = Input::get('fecha_fin');

        if (empty($id_empleado) || empty($id_horario_plantilla) || empty($fecha_inicio) || empty($fecha_fin)) {
            return $this->retornarError();
        }

        if (!($empleado = Empleado::find($id_empleado))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $empleado->asignarHorarioDesdePlantilla($id_horario_plantilla, $fecha_inicio, $fecha_fin);

        return $this->retornar(self::MSJ_GUARDADO);
    }


    public function quitarEventosPost() {
        $id_empleado = (int)Input::get('id_empleado');
        $fecha_inicio = Input::get('fecha_inicio');
        $fecha_fin = Input::get('fecha_fin');

        if (empty($id_empleado) || empty($fecha_inicio) || empty($fecha_fin)) {
            return $this->retornarError();
        }

        if (!($empleado = Empleado::find($id_empleado))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $empleado->quitarHorario($fecha_inicio, $fecha_fin);

        return $this->retornar('Eventos eliminados.');
    }


    public function cargarEventosGet() {
        $id = (int)Input::get('id');

        if (empty($id)) {
            return $this->retornarError();
        }

        if (!($horario = Horario::find($id))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $eventos = $horario->listaEventos();

        $this->especificarRespuesta('id', (int)$horario->id);
        $this->especificarRespuesta('eventos', $eventos);
        return $this->retornar();
    }


    public function registrarEventoPost() {
        $id_horario = (int)Input::get('id_horario');
        $id_empleado = (int)Input::get('id_empleado');
        $tipo = (int)Input::get('tipo');
        $horas = (float)Input::get('horas');
        $comentario = Input::get('comentario');

        if (empty($id_horario) || empty($id_empleado)) {
            return $this->retornarError();
        }

        if (!($horario = Horario::find($id_horario))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $archivo = ArchivoController::subirArchivoParaItem(null, 'archivo');

        $horario->guardarEvento($tipo, $horas, $comentario, $archivo);

        return $this->retornar(self::MSJ_GUARDADO);
    }


    public function eliminarEventoItemPost() {
        $id_horario_evento = (int)Input::get('id_horario_evento');

        if (!($horario_evento = HorarioEvento::find($id_horario_evento))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $horario_evento->delete();

        $this->especificarRespuesta('id', $id_horario_evento);
        return $this->retornar();
    }

}