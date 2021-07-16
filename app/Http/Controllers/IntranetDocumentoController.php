<?php

namespace App\Http\Controllers;

use App\IntranetDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
class IntranetDocumentoController extends Controlador
{
    protected $modelo = 'IntranetDocumento';
    public function editarDocumentoPost() {
        

        $id = (int)Input::get('id');
        $obj = new IntranetDocumento;
        $validacion = Validator::make(
            Input::all(),
            $obj->reglasValidacion(null, $id)
        );

        if ($validacion->passes()) {

            $item = IntranetDocumento::find($id);
            if ($item) {
                $item->update(Input::all());
                $item->save();
                $this->especificarRespuesta('item', $item);
                $this->especificarRespuesta('msj', __('global.saved_msg'));

            }
            else {
                return $this->retornarError(self::ERROR_NO_ENCONTRADO);
            }
        }
        else {
            //$msj = $validacion->errors()->first();
            list($msj, $campo) = $this->mensajeYCampoDeError($validacion);
            return $this->retornarError($msj, $campo);
        }

        return $this->retornar();
    }

}
