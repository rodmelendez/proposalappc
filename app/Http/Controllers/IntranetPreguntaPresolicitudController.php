<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IntranetPreguntaPresolicitud;
use App\IntranetPresolicitud;

use Illuminate\Support\Facades\Input;

class IntranetPreguntaPresolicitudController extends Controlador
{
    protected $modelo = 'IntranetPreguntaPresolicitud';

    public function preguntasGet()
    {
        $presolicitud = IntranetPresolicitud::find(Input::get('id'));
        $preguntas = $presolicitud->preguntas()->get();
        foreach ($preguntas as $pregunta) {
            $pregunta->respuestas = $pregunta->respuestas()->get();
            $pregunta->usuarioPregunta = $pregunta->usuario()->first();
            foreach ($pregunta->respuestas as $respuesta) {
                $respuesta->usuario = $respuesta->usuario()->first();
            }
        }
        $this->especificarRespuesta('preguntas',$preguntas);
        return $this->retornar();

    }
    
    public function respuestaPreguntaGet()
    {
        $pregunta = IntranetPreguntaPresolicitud::find(Input::get('id_pregunta'));
        $pregunta->respuestas = $pregunta->respuestas()->get();
        $pregunta->usuarioPregunta = $pregunta->usuario()->first();
        foreach ($pregunta->respuestas as $respuesta) {
            $respuesta->usuario = $respuesta->usuario()->first();
        }
        $this->especificarRespuesta('pregunta',$pregunta);
        return $this->retornar();

    }
}
