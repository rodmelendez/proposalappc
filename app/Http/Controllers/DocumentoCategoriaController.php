<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 14/6/2019
 * Time: 6:45 PM
 */

namespace App\Http\Controllers;


use App\Documento;
use App\DocumentoCategoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class DocumentoCategoriaController extends Controlador {

    protected $modelo = 'DocumentoCategoria';


    public function indexGet() {
        $items = DocumentoCategoria::traerData();

        foreach ($items as $key => $item) {
            $items[$key]['archivos'] = DocumentoCategoria::listaDocumentosDe($item['id']);
        }

        return $items;
    }


    public function cargarGet() {
        $id_documento_categoria = (int)Input::get('id_padre');

        $items = DocumentoCategoria::orderBy('nombre');

        if (!empty($id_documento_categoria)) {
            if (!($documento_categoria = DocumentoCategoria::find($id_documento_categoria))) {
                return $this->retornarError(self::ERROR_NO_ENCONTRADO);
            }

            $items = $items->where('id_documento_categoria', '=', (int)$id_documento_categoria);
        }

        $items = $items
            ->get(['id', 'nombre'])
            ->toArray();

        foreach ($items as $key => $item) {
            $items[$key]['archivos'] = DocumentoCategoria::listaDocumentosDe($item['id']);//$item->listaDocumentos();
        }

        $this->especificarRespuesta('items', $items);
        return $this->retornar();
    }


    public function guardarCategoriaPost() {
        $nombre = Input::get('nombre');
        $id_documento_categoria = (int)Input::get('id_padre');

        if (!empty($nombre)) {
            //se valida el nombre del campo
            {
                $validacion = Validator::make(
                    Input::all(),
                    DocumentoCategoria::reglasValidacion()
                );
                if ($validacion->fails()) {
                    list($msj, $campo) = $this->mensajeYCampoDeError($validacion);
                    return $this->retornarError($msj, $campo);
                }
            }

            if (DocumentoCategoria::where('nombre', '=', $nombre)->where('id_documento_categoria', '=', $id_documento_categoria)->count()) {
                return $this->retornarError('Categoría ya existente.');
            }

            $dc = DocumentoCategoria::create([
                'nombre' => $nombre,
                'id_documento_categoria' => $id_documento_categoria ?: null,
            ]);

            $this->especificarRespuesta('item', [
                'id' => $dc->id,
                'nombre' => $dc->nombre,
            ]);
        }

        return $this->retornar(self::MSJ_GUARDADO);
    }


    public function actualizarCategoriaPost() {
        $id_documento_categoria = (int)Input::get('id');
        $nombre = Input::get('nombre');

        if (!($documento_categoria = DocumentoCategoria::find($id_documento_categoria))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        //se valida el nombre del campo
        {
            $validacion = Validator::make(
                Input::all(),
                DocumentoCategoria::reglasValidacion()
            );
            if ($validacion->fails()) {
                list($msj, $campo) = $this->mensajeYCampoDeError($validacion);
                return $this->retornarError($msj, $campo);
            }
        }

        $documento_categoria->nombre = $nombre;
        $documento_categoria->save();

        $this->especificarRespuesta('nombre', $nombre);
        return $this->retornar(self::MSJ_GUARDADO);
    }


    public function eliminarCategoriaPost() {
        $id_documento_categoria = (int)Input::get('id');

        if (!($documento_categoria = DocumentoCategoria::find($id_documento_categoria))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $documento_categoria->delete();

        $this->especificarRespuesta('id', $id_documento_categoria);
        return $this->retornar(self::MSJ_ELIMINADO);
    }


    public function subirArchivoPost() {
        $usuario = Auth::user();

        if (!$usuario) {
            return $this->retornarError('Sesión finalizada.');
        }

        $id_documento_categoria = (int)Input::get('id_documento_categoria');
        $nombre_archivo_original = ArchivoController::nombreArchivo('archivo');

        if (!$id_documento_categoria || !$nombre_archivo_original) {
            return $this->retornarError('No se ha especificado un archivo.');
        }

        $nombre_archivo = ArchivoController::subirArchivoParaItem(null, 'archivo');

        if (!$nombre_archivo) {
            return $this->retornarError('No se pudo subir el archivo.');
        }

        $documento = Documento::create([
            'id_documento_categoria' => $id_documento_categoria,
            'nombre' => pathinfo($nombre_archivo_original, PATHINFO_FILENAME),
            'archivo' => $nombre_archivo,
            'id_usuario' => $usuario->id,
        ]);

        if (!$documento) {
            return $this->retornarError();
        }

        $archivo = $documento->archivos()->create([
            'archivo' => $nombre_archivo,
            'id_usuario' => $usuario->id,
        ]);

        if (!$archivo) {
            return $this->retornarError();
        }

        $this->especificarRespuesta('item', [
            'id' => $documento->id,
            'nombre' => $documento->nombre,
            'archivo' => $documento->archivo,
        ]);
        return $this->retornar(self::MSJ_GUARDADO);
    }

}