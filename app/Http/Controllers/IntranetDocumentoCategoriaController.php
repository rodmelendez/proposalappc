<?php

namespace App\Http\Controllers;

use App\IntranetDocumentoCategoria;
use App\IntranetDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
class IntranetDocumentoCategoriaController extends Controlador
{
    protected $modelo = 'IntranetDocumentoCategoria';
    
    public function indexGet() {
        $items = IntranetDocumentoCategoria::traerData();

        foreach ($items as $key => $item) {
            $items[$key]['archivos'] = IntranetDocumentoCategoria::listaDocumentosDe($item['id']);
        }

        return $items;
    }
 
    public function guardarCategoriaPost() {

        $nombre = Input::get('nombre');
        $id_documento_categoria = (int)Input::get('id_documento_categoria');
        $user = Auth::user();
        $documento = IntranetDocumentoCategoria::find($id_documento_categoria);
        if($documento['id_documento_categoria'] !== null){
            return $this->retornarError('no se pueden crear, sub carpetas recursivas.');
        }
        if (!empty($nombre)) {
            //se valida el nombre del campo
            {
                $validacion = Validator::make(
                    Input::all(),
                    IntranetDocumentoCategoria::reglasValidacion()
                );
                if ($validacion->fails()) {
                    list($msj, $campo) = $this->mensajeYCampoDeError($validacion);
                    return $this->retornarError($msj, $campo);
                }
            }

            if (IntranetDocumentoCategoria::where('nombre', '=', $nombre)->where('id_documento_categoria', '=', $id_documento_categoria)->count()) {
                return $this->retornarError('Categoría ya existente.');
            }
            

            $subCarpetas = IntranetDocumentoCategoria::where([['id_documento_categoria','=',$id_documento_categoria],['fecha_eliminacion','=',null]])->count();
            if($id_documento_categoria && $subCarpetas < 5 ){
           
                $dc = IntranetDocumentoCategoria::create([
                    'nombre' => $nombre,
                    'id_documento_categoria' => $id_documento_categoria,
                    'id_usuario' => $user->id ? $user->id :null,
                ]);        
                $this->especificarRespuesta('item', [
                    'id' => $dc->id,
                    'nombre' => $dc->nombre,
                ]);
            }else{
                $this->retornarError('no se puedo crear la carpeta');
                $this->retornar();            
            }

        }
        return $this->retornar(self::MSJ_GUARDADO);
    }

    public function cargarGet() {

        $id_documento_categoria = (int)Input::get('id_documento_categoria');

        $items = IntranetDocumentoCategoria::orderBy('nombre');

        if (!empty($id_documento_categoria)) {
            if (!($documento_categoria = IntranetDocumentoCategoria::find($id_documento_categoria))) {
                return $this->retornarError(self::ERROR_NO_ENCONTRADO);
            }

            $items = $items->where('id_documento_categoria', '=', (int)$id_documento_categoria);
            
        }

        $items = $items
            ->get(['id', 'nombre'])
            ->toArray();
        

        foreach ($items as $key => $item) {
            $items[$key]['archivos'] = IntranetDocumentoCategoria::listaDocumentosDe($item['id']);//$item->listaDocumentos();
        }
        $items['archivos'] =  IntranetDocumentoCategoria::listaDocumentosDe($id_documento_categoria);

        

        $this->especificarRespuesta('items', $items);

        return $this->retornar();
    }


    public function actualizarCategoriaPost() {
        $id_documento_categoria = (int)Input::get('id');
        $nombre = Input::get('nombre');

        if (!($documento_categoria = IntranetDocumentoCategoria::find($id_documento_categoria))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        //se valida el nombre del campo
        {
            $validacion = Validator::make(
                Input::all(),
                IntranetDocumentoCategoria::reglasValidacion()
            );
            if ($validacion->fails()) {
                list($msj, $campo) = $this->mensajeYCampoDeError($validacion);
                return $this->retornarError($msj, $campo);
            }
        }
        if($documento_categoria->id_documento_categoria !== null){

            $documento_categoria->nombre = $nombre;
            $documento_categoria->save();
    
            $this->especificarRespuesta('nombre', $nombre);
            return $this->retornar(self::MSJ_GUARDADO);
        }else{
            $this->retornarError('las carpetas padres no se pueden modificar');
            $this->retornar(); 
        }
    }




    public function eliminarCategoriaPost() {
        $id = (int)Input::get('id');

        if (!($documento_categoria = IntranetDocumentoCategoria::find($id))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }
        if($documento_categoria->id_documento_categoria !== null){

            $documento_categoria->delete();
    
            $this->especificarRespuesta('eliminado', $id);
            return $this->retornar(self::MSJ_ELIMINADO);
        }else{
            $this->retornarError('no se puedo eliminar');
             $this->retornar();
        }

    }
    ////// API ////
    public static function cargarCarpetasGet(){
        $items = IntranetDocumentoCategoria::traerData();

        foreach ($items as $key => $item) {
            $items[$key]['archivos'] = IntranetDocumentoCategoria::listaDocumentosDe($item['id']);
        }

        return $items;
    }

    public static function  cargarSubcarpetaGet($id)
    {
        $id_documento_categoria = (int)$id;

        $items = IntranetDocumentoCategoria::orderBy('nombre');

        if (!empty($id_documento_categoria)) {
            if (!($documento_categoria = IntranetDocumentoCategoria::find($id_documento_categoria))) {
                
                return null;
            }

            $items = $items->where('id_documento_categoria', '=', (int)$id_documento_categoria);
            
        }

        $items = $items
            ->get(['id', 'nombre'])
            ->toArray();
        

        foreach ($items as $key => $item) {
            $items[$key]['archivos'] = IntranetDocumentoCategoria::listaDocumentosDe($item['id']);//$item->listaDocumentos();
        }
        $items['archivos'] =  IntranetDocumentoCategoria::listaDocumentosDe($id_documento_categoria);

        

        return $items;   
    }
   


}
