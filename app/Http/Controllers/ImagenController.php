<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;

class ImagenController {

/**
 * Sube la imagen según el campo de un item
 *
 * @param $item
 * @param $nombre_campo
 * @param string $tamanos
 * @return bool|mixed|string
 */
public static function subirImagenParaItem($item, $nombre_campo, $tamanos = 'lms') {
    $ha_cambiado = Input::get($nombre_campo . '_upload_modificado');
    $nombre = Input::get($nombre_campo);

    //cuando se ha modificado y se envía una imagen
    if ($ha_cambiado && Input::hasFile($nombre_campo . '_upload')) {

        //imagen enviada
        $archivo = Input::file($nombre_campo . '_upload');

        if ($archivo->isValid()) {
            //extrae la extensión del archivo (jpg, png)
            $extension = strtolower($archivo->getClientOriginalExtension());

            //ruta donde se guardará el archivo
            $ruta_destino = config('app.uploads_img_dir');

            //nombre que se le dará al archivo
            $nombre_archivo = uniqid() . '.' . $extension; //$file->getClientOriginalName();

            //si el nombre ya existe (no debería suceder), se intenta hasta que consiga uno que no exista
            $esc = 0;
            while (file_exists($ruta_destino . '/' . $nombre_archivo)) {
                usleep(100);
                $nombre_archivo = uniqid() . '.' . $extension;
                $esc++;
                if ($esc >= 30) return false; // !!!
            }

            //copia la imagen original al destino
            $copiado = $archivo->move($ruta_destino, $nombre_archivo);

            if ($copiado) {
                if (strpos($tamanos, 's') !== false) {
                    self::redimensionar($ruta_destino . '/' . $nombre_archivo, null, 256, 256, true, $ruta_destino . '/s/' . $nombre_archivo, false);
                }
                if (strpos($tamanos, 'm') !== false) {
                    self::redimensionar($ruta_destino . '/' . $nombre_archivo, null, 512, 512, true, $ruta_destino . '/m/' . $nombre_archivo, false);
                }
                if (strpos($tamanos, 'l') !== false) {
                    self::redimensionar($ruta_destino . '/' . $nombre_archivo, null, 1024, 1024, true, $ruta_destino . '/l/' . $nombre_archivo, false);
                }

                //si se pasa el objeto, se actualiza el campo correspondiente
                if ($item !== null) {
                    $imagen_vieja = $item->$nombre_campo;

                    $item->$nombre_campo = $nombre_archivo;
                    $item->save();

                    //elimina la foto anterior del disco duro
                    if (!empty($imagen_vieja)) {
                        self::eliminarImagen($imagen_vieja);
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

    return false;
}


/**
 * Elimina un archivo del directorio de uploads
 *
 * @param $nombre_archivo
 */
public static function eliminarImagen($nombre_archivo) {
    //ruta donde se guarda el archivo
    $ruta_destino = config('app.uploads_img_dir');

    if ($ruta_destino && $nombre_archivo) {
        try {
            @unlink($ruta_destino . '/' . $nombre_archivo);
            @unlink($ruta_destino . '/l/' . $nombre_archivo);
            @unlink($ruta_destino . '/m/' . $nombre_archivo);
            @unlink($ruta_destino . '/s/' . $nombre_archivo);
        } catch (Exception $e) {}
    }
}


public static function atributos($nombre_imagen, $simplificado = true) {
    $ruta_destino = config('app.uploads_img_dir');

    /*$fp = fopen(public_path($ruta_destino) . DIRECTORY_SEPARATOR .  $nombre_imagen, 'rb');

    if (!$fp) {
        return null;
    }*/

    $headers = @exif_read_data(public_path($ruta_destino) . DIRECTORY_SEPARATOR .  $nombre_imagen/*$fp*/);

    if (!$headers) {
        return null;
    }

    if ($simplificado) {
        return [
            'ancho' => $headers['COMPUTED']['Width'] ?? $headers['COMPUTED']['Width'],
            'alto' => $headers['COMPUTED']['Height'] ?? $headers['COMPUTED']['Height'],
            'kbs' => (int)round(($headers['FileSize'] ?? 0) / 1024),
            'timestamp' => $headers['FileDateTime'] ?? null,
            'fecha' => !empty($headers['FileDateTime']) ? date('Y-m-d H:i:s', $headers['FileDateTime']) : null,
            'marca' => $headers['Make'] ?? '',
            'modelo' => $headers['Model'] ?? '',
            'coordenadas' => $headers['GPS'] ?? null,
        ];
    }

    return $headers;
}


public static function rotar($nombre_imagen, $angulo = 90) {
    $ruta_destino = config('app.uploads_img_dir');
    $tamanos = ['original', 'l', 'm', 's'];

    $tipo = null;

    foreach ($tamanos as $tamano) {
        $path_imagen = public_path($ruta_destino) . DIRECTORY_SEPARATOR . ($tamano != 'original' ? ($tamano . DIRECTORY_SEPARATOR) : '') . $nombre_imagen;
        $info = getimagesize($path_imagen);

        switch ($info[2]) {
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($path_imagen);
                break;

            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($path_imagen);
                break;

            default:
                $source = imagecreatefromjpeg($path_imagen);
        }

        if (!$source) {
            continue;
        }

        $img_rotated = imagerotate($source, $angulo, 0);

        $quality = 75;
        $output = $source;

        //imagejpeg($rotate, $source);
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                imagejpeg($img_rotated, $path_imagen, $quality);
                $tipo = 1;
                break;

            case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9*$quality)/10.0);
                imagepng($img_rotated, $output, $quality);
                $tipo = 2;
                break;

            case IMAGETYPE_GIF:
                imagegif($img_rotated, $output);
                $tipo = 3;
                break;
        }

        usleep(100);
    }

    return $tipo;
}


public static function crearDesdeBase64($data) {
    $ruta_destino = config('app.uploads_img_dir');
    $nombre_imagen = uniqid() . '.jpg';
    $path = public_path($ruta_destino) . DIRECTORY_SEPARATOR;
    $path_imagen = $path . $nombre_imagen;

    $img_data = base64_decode($data);
    $source = imagecreatefromstring($img_data);

    $img = imagejpeg($source, $path_imagen, 80);

    if (!$img) {
        return null;
    }

    $tamanos = [
        'l' => 1024,
        'm' => 512,
        's' => 256
    ];

    foreach ($tamanos as $tamano => $dimension) {
        usleep(100);
        self::redimensionar($path_imagen, null, $dimension, $dimension, true, $path . $tamano . DIRECTORY_SEPARATOR . $nombre_imagen, false);
    }

    imagedestroy($source);

    return $nombre_imagen;
}


/**
 * Procedimiento para redimensionar una imagen y guardarla
 *
 * @param $file
 * @param null $string
 * @param int $width
 * @param int $height
 * @param bool $proportional
 * @param string $output
 * @param bool $delete_original
 * @param bool $use_linux_commands
 * @param int $quality
 * @return bool
 */
public static function redimensionar($file, $string = null, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false, $quality = 100) {
    if ( $height <= 0 && $width <= 0 ) return false;
    if ( $file === null && $string === null ) return false;

    # Setting defaults and meta
    $info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
    $image                        = '';
    $final_width                  = 0;
    $final_height                 = 0;
    list($width_old, $height_old) = $info;
    $cropHeight = $cropWidth = 0;

    if ($proportional) {
        if      ($width  == 0)  $factor = $height/$height_old;
        elseif  ($height == 0)  $factor = $width/$width_old;
        else                    $factor = min( $width / $width_old, $height / $height_old );

        $final_width  = round( $width_old * $factor );
        $final_height = round( $height_old * $factor );
    }
    else {
        $final_width = ( $width <= 0 ) ? $width_old : $width;
        $final_height = ( $height <= 0 ) ? $height_old : $height;
        $widthX = $width_old / $width;
        $heightX = $height_old / $height;

        $x = min($widthX, $heightX);
        $cropWidth = ($width_old - $width * $x) / 2;
        $cropHeight = ($height_old - $height * $x) / 2;
    }

    switch ( $info[2] ) {
        case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
        case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
        case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
        default: return false;
    }

    $image_resized = imagecreatetruecolor($final_width, $final_height);

    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
        $transparency = imagecolortransparent($image);
        $palletsize = imagecolorstotal($image);

        if ($transparency >= 0 && $transparency < $palletsize) {
            $transparent_color  = imagecolorsforindex($image, $transparency);
            $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
            imagefill($image_resized, 0, 0, $transparency);
            imagecolortransparent($image_resized, $transparency);
        }
        elseif ($info[2] == IMAGETYPE_PNG) {
            imagealphablending($image_resized, false);
            $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
            imagefill($image_resized, 0, 0, $color);
            imagesavealpha($image_resized, true);
        }
    }

    imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);

    if ($delete_original) {
        if ($use_linux_commands) exec('rm '.$file);
        else @unlink($file);
    }

    switch (strtolower($output)) {
        case 'browser':
            $mime = image_type_to_mime_type($info[2]);
            header("Content-type: $mime");
            $output = NULL;
            break;

        case 'file':
            $output = $file;
            break;

        case 'return':
            return $image_resized;
            break;

        default:
            break;
    }

    switch ( $info[2] ) {
        case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
        case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
        case IMAGETYPE_PNG:
            $quality = 9 - (int)((0.9*$quality)/10.0);
            imagepng($image_resized, $output, $quality);
            break;
        default: return false;
    }

    return true;
}

public static function output() {
    $src = Input::get('src');
    $w = (int)Input::get('w', 0);
    $h = (int)Input::get('h', 0);
    $crop = (bool)Input::get('c', false);

    if (empty($src)) exit;

    $path = public_path(config('app.uploads_img_dir')) . DIRECTORY_SEPARATOR;

    $img = $path . $src;

    self::redimensionar($img, null, $w, $h, !$crop, 'browser', false);
}


public static function prePost() {
    return true;
}


public function subirImagenPost() {
    $nombre_imagen = self::subirImagenParaItem(null, 'imagen');

    if (empty($nombre_imagen)) {
        return [
            'ok' => 0,
            'err' => Input::hasFile('imagen_upload') ? __('global.image_not_found') : __('global.unable_save_image'),
            'imagen' => '',
        ];
    }

    return [
        'ok' => 1,
        'nombre_imagen' => $nombre_imagen,
        'imagen' => asset(config('app.uploads_img_dir') . '/' . $nombre_imagen),
    ];
}


public static function urlImagen($nombre_foto = null, $tamano = 's', $defecto = null) {
    if ($defecto === null) {
        $img_defecto = asset('img/img-placeholder.png');
    }
    else {
        if ($defecto === false && empty($nombre_foto)) return null;
        $img_defecto = $defecto;
    }
    if (!empty($tamano)) $tamano = '/' . $tamano;
    return !empty($nombre_foto) ? asset(config('app.uploads_img_dir') . $tamano . '/' . $nombre_foto) : $img_defecto;
}

}
