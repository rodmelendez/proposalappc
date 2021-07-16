<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 10/7/2019
 * Time: 9:41 PM
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CodigoQrController extends Controlador {

    protected $modelo = 'CodigoQr';


    public static function generar($params, $formato = null) {
        $tipo = intval($params['tipo'] ?? 0);

        $tamano = intval($params['tamano'] ?? 400) ?: 400;

        switch ($tipo) {
            case 0:
                $url = $params['url'] ?: url('/');

                $qr = QrCode
                    ::format($formato ?: 'svg')
                    ->size($tamano)
                    ->generate($url);

                break;

            case 1:
                $nombre = $params['nombre'] ?? '';
                $apellido = $params['apellido'] ?? '';
                $telefono = $params['telefono'] ?? '';
                $correo = $params['correo'] ?? '';
                $empresa = $params['empresa'] ?? '';
                $cargo = $params['cargo'] ?? '';
                $direccion = $params['direccion'] ?? '';

                $fecha = date('Y-m-d');
                $hora = date('H:i:s');

                $vcard = <<<EOT
BEGIN:VCARD
VERSION:3.0
N:{$apellido};{$nombre};;;
FN:{$nombre} {$apellido}
ORG:{$empresa}
TEL;TYPE=WORK,VOICE:${telefono}
ADR;TYPE=WORK,PREF:${direccion};;;;;;
LABEL;TYPE=WORK,PREF:${direccion}
EMAIL:{$correo}
REV:{$fecha}T{$hora}Z
END:VCARD
EOT;

                /*$fecha = date('Ymd');
                $hora = date('His');

                $vcard = <<<EOT
BEGIN:VCARD
VERSION:2.1
N:{$apellido};{$nombre};;
FN:{$nombre} {$apellido}
ORG:{$empresa}
TEL;WORK;VOICE:{$telefono}
ADR;WORK;PREF:;;;;;;
LABEL;WORK;PREF;ENCODING=QUOTED-PRINTABLE;CHARSET=UTF-8:${direccion}
EMAIL:{$correo}
REV:{$fecha}T{$hora}Z
END:VCARD
EOT;*/

                //ADR;TYPE=HOME;LABEL="${direccion}":;;STREET;CITY;STATE_CODE;POSTAL_CODE;COUNTRY_NAME

                $qr = QrCode
                    ::format($formato ?: 'svg')
                    ->size($tamano)
                    ->generate($vcard);

                break;

            default:
                $qr = '';
        }

        return $qr;
    }


    public function generarGet() {
        $qr = self::generar(Input::all());

        $this->especificarRespuesta('qr_code', $qr);
        return $this->retornar();
    }


    public function descargarPost() {
        $qr = self::generar(Input::all(), 'png');

        header("Content-Type: image/png");
        header("Content-Length: " . strlen($qr));
        header("Content-Disposition: attachment; filename=codigo_qr.png");
        header('Content-Transfer-Encoding: binary');
        //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        echo $qr;
        exit;
    }

}