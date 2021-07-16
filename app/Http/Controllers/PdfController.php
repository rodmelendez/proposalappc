<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 2/5/2019
 * Time: 11:06 AM
 */

namespace App\Http\Controllers;


use PDF;

class PdfController {

    public static function generar($vista, $data = [], $attrs = [], $nombre = null) {
        if (empty($nombre)) {
            $nombre = uniqid('doc_');
        }

        $pdf = PDF::loadView('layouts.pdf.' . $vista, $data);

        //$pdf = new mPDF('es', 'A4', 0, '', 5, 5, 0, 0, 0, 0, 'P');

        //$pdf->SetDefaultBodyCSS('background-image-resize', 6);

        foreach ($attrs as $attr => $valor) {
            switch ($attr) {
                case 'margen':

                    break;
            }
        }

        return $pdf->stream($nombre . '.pdf');
    }
}