<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 10-Apr-2018
 * Time: 8:24 PM
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;

class ArchivoController {

    /**
     * Sube el archivo según el campo de un item
     *
     * @param $item
     * @param $nombre_campo
     * @return string|mixed
     */
    public static function subirArchivoParaItem($item, $nombre_campo) {
        $ha_cambiado = Input::get($nombre_campo . '_upload_modificado');
        $nombre = Input::get($nombre_campo);

        //cuando se ha modificado y se envía un archivo
        if ($ha_cambiado && Input::hasFile($nombre_campo . '_upload')) {

            //archivo enviado
            $archivo = Input::file($nombre_campo . '_upload');

            if ($archivo->isValid()) {
                //extrae la extensión del archivo (pdf, docx)
                $extension = strtolower($archivo->getClientOriginalExtension());

                //ruta donde se guardará el archivo
                $ruta_destino = config('app.uploads_doc_dir');

                //nombre que se le dará al archivo
                $nombre_archivo = uniqid() . '.' . $extension; //$file->getClientOriginalName();

                //si el nombre ya existe (no debería suceder), se intenta hasta que consiga uno que no exista
                $esc = 0;
                while (file_exists($ruta_destino . '/' . $nombre_archivo)) {
                    usleep(100);
                    $nombre_archivo = uniqid() . '.' . $extension;
                    $esc++;
                    if ($esc >= 30) return null; // !!!
                }

                //copia el archivo original al destino
                $copiado = $archivo->move($ruta_destino, $nombre_archivo);

                if ($copiado) {
                    //si se pasa el objeto, se actualiza el campo correspondiente
                    if ($item !== null) {
                        $archivo_viejo = $item->$nombre_campo;

                        $item->$nombre_campo = $nombre_archivo;
                        $item->save();

                        //elimina el archivo anterior del disco duro
                        if (!empty($archivo_viejo)) {
                            self::eliminarImagen($archivo_viejo);
                        }
                    }

                    return $nombre_archivo;
                }
            }
        }

        //cuando se ha cambiado pero no se ha enviado una imagen (fue borrada)
        elseif ($ha_cambiado) {
            if ($item !== null) {
                $item->$nombre_campo = '';
                $item->save();
            }
            return '';
        }

        //cuando no se ha modificado, se retorna el mismo nombre ya asignado
        elseif ($nombre) {
            return $nombre;
        }

        return null;
    }


    public static function nombreArchivo($nombre_campo) {
        $archivo = Input::file($nombre_campo . '_upload');

        if ($archivo->isValid()) {
            return $archivo->getClientOriginalName();
        }

        return null;
    }

    public static function urlDocumento($documento = null) {
       
       
        if ( empty($documento)) return null;
           
        return asset(config('app.uploads_doc_dir') . $documento) ;
    }
    
    /**
     * Elimina un archivo del directorio de uploads
     *
     * @param $nombre_archivo
     */
    public static function eliminarArchivo($nombre_archivo) {
        //ruta donde se guarda el archivo
        $ruta_destino = config('app.uploads_doc_dir');

        if ($ruta_destino && $nombre_archivo) {
            try {
                @unlink($ruta_destino . '/' . $nombre_archivo);
            } catch (Exception $e) {}
        }
    }

}