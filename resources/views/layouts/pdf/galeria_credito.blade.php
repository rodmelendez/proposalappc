@php
    $total_paginas = max(1, ceil(count($items) / $items_por_pagina));

    if (empty($orientacion) || $orientacion == 'vertical') {
        $width = 8.5;
        $height = 11;

        switch ($items_por_pagina) {
            case 1: /* good */
                $margen = 'auto';
                $ancho = 6.9;
                $alto = 6.9;
                $dimension_x = 1000;
                $dimension_y = 1000;
                $break = true;
                break;

            case 4: /* good */
                $ancho = 3.4;
                $alto = 3.4;
                $dimension = 600;
                $break = true;
                break;

            default: // 9 good
                $ancho = 2.3;
                $alto = 2.0;
                $dimension_x = 320;
                $dimension_y = 290;
                $break = true;
                break;
        }
    }
    else { //horizontal
        $width = 11;
        $height = 8.5;

        switch ($items_por_pagina) {
            case 1: /* good */
                $margen = 'auto';
                $ancho = 9.2;
                $alto = 4.6;
                $dimension_x = 2000;
                $dimension_y = 1000;
                $break = true;
                break;

            case 2: /* good */
                $ancho = 4.8;
                $alto = 4.8;
                $dimension = 700;
                $break = true;
                break;

            case 3:
                $ancho = 3.2;
                $alto = 3.2;
                $dimension = 600;
                $break = true;
                break;

            case 4:
                //$margen = '1.5in';
                $ancho = 2.4;
                $alto = 2.4;
                $dimension = 600;
                $break = true;
                break;

            case 6: /* good */
                $ancho = 3.2;
                $alto = 2.0;
                $dimension_x = 780;
                $dimension_y = 500;
                $break = true;
                break;

            default: // 9
                $ancho = 2.3;
                $alto = 2.3;
                $dimension = 300;
                break;
        }
    }

    $n = 0;
    $nc = 0;
    $p = 1;
@endphp
<style>
    @page {
        sheet-size: {!! $width !!}in {!! $height !!}in;
        margin: 0.5in;
    }
</style>
<div class="galeria-print" style="width:{!! $width !!}in; padding:.2in;">
    @include('layouts.pdf.galeria_credito_cabecera', [
        'logo' => $logo,
        'num' => $num,
        'nombre' => $nombre,
        'monto' => $monto,
        'nombre_cliente' => $nombre_cliente,
        'dni_cliente' => $dni_cliente,
        'direccion_cliente' => $direccion_cliente,
        'tipo_cliente' => $tipo_cliente,
        'negocio_cliente' => $negocio_cliente,
        'ruc_cliente' => $ruc_cliente,
        'telefono_cliente' => $telefono_cliente,
        'pagina' => 1,
        'total_paginas' => $total_paginas,
        'fecha' => $fecha,
        'usuario' => $usuario,
        'moneda' => $moneda,
    ])

    <div style="width:100%; margin-top:.2in; margin-bottom:.2in; {!! !empty($margen) ? ('margin-left:' . $margen . '; margin-right:' . $margen . ';') : '' !!}">
        @foreach ($items as $item)
            <div class="galeria-item-print" style="width:{!! $ancho !!}in; height:{!! $alto !!}in; float:left; margin-bottom:.1in;">
                <div style="padding:.1in; margin:.1in; border:.01in solid #444; border-radius:.1in;">

                    <div style="width:{!! $ancho - .4 !!}in; height:{!! $alto - .4 !!}in; position:relative;">
                        <img src="{!! route('imagen', ['src'=>$item->foto, 'w'=>$dimension_x ?? $dimension, 'h'=>$dimension_y ?? $dimension, 'c'=>1]) !!}" alt="" style="width:100%">

                        @if (!empty($tipos[$item->tipo]))
                            <div style="float:left; background-color:#fff; border-radius:50%; width:12px; height:12pt; margin-top:4pt; margin-bottom:-12pt;">
                                <img src="{!! asset('img/' . $tipos[$item->tipo]['icono_bw']) !!}" alt="" style="width:10pt; height:10pt;">
                            </div>
                        @endif
                    </div>

                    <div class="galeria-item-titulo" style="text-align:center; font-size:8pt; padding-left:10pt; padding-top:2pt; line-height:9pt;">{!! $item->nombre ?: $tipos[$item->tipo]['nombre'] !!}</div>

                </div>
            </div>

            @php
                $n++;

                if (!empty($cols)) {
                    $nc++;
                    if ($nc == $cols) {
                        echo '<div style="clear:both">&nbsp;</div>';
                        $nc = 0;
                    }
                }
            @endphp

            @if ($n == $items_por_pagina && $p < $total_paginas)
                @include('layouts.pdf.galeria_credito_pie', [
                    'empresa_telefono' => $empresa_telefono,
                    'empresa_website' => $empresa_website,
                    'observaciones' => $observaciones,
                    'break' => !empty($break)
                ])

                @php
                    $n = 0;
                    //echo '</div> <div class="page-break" style="page-break-before:always"></div>';
                @endphp

                @include('layouts.pdf.galeria_credito_cabecera', [
                    'logo' => $logo,
                    'num' => $num,
                    'nombre' => $nombre,
                    'monto' => $monto,
                    'nombre_cliente' => $nombre_cliente,
                    'dni_cliente' => $dni_cliente,
                    'direccion_cliente' => $direccion_cliente,
                    'tipo_cliente' => $tipo_cliente,
                    'negocio_cliente' => $negocio_cliente,
                    'ruc_cliente' => $ruc_cliente,
                    'telefono_cliente' => $telefono_cliente,
                    'pagina' => ++$p,
                    'total_paginas' => $total_paginas,
                    'fecha' => $fecha,
                    'usuario' => $usuario,
                    'moneda' => $moneda,
                ])

                @php
                    //echo '<div style="margin-top:.5in; margin-bottom:.5in;">';
                @endphp
            @endif
        @endforeach
    </div>

    {{--<div style="clear:both;"></div>--}}

    @include('layouts.pdf.galeria_credito_pie', [
        'empresa_telefono' => $empresa_telefono,
        'empresa_website' => $empresa_website,
        'observaciones' => $observaciones,
    ])
</div>