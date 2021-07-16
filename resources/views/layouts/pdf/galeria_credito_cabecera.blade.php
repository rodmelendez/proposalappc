<table style="border:0; border-collapse:collapse; width:100%">
    <thead>
        <tr>
            @if ($logo)
                <td style="width:30%">
                    <img src="{!! asset('uploads/img/' . $logo) !!}" style="width:1.4in;" alt="">
                </td>
            @endif
            <td style="width:40%; text-align:center;">
                <h2 style="margin-top:.05in; margin-bottom:.1in;">{{ $nombre }}</h2>
                <div style="font-size:8pt;"><strong>Cliente:</strong> {!! $nombre_cliente !!} {!! !empty($dni_cliente) ? '(' : '' !!}{!! $dni_cliente !!}{!! !empty($dni_cliente) ? ')' : '' !!}</div>
                @if (!empty($direccion_cliente))
                    <div style="font-size:8pt;"><strong>Dirección:</strong> {!! $direccion_cliente !!}</div>
                @endif
                @if ($tipo_cliente == 2 /*juridico*/)
                    <div style="font-size:8pt;"><strong>Negocio:</strong> {!! $negocio_cliente !!} {!! !empty($ruc_cliente) ? '(' : '' !!}{!! $ruc_cliente !!}{!! !empty($ruc_cliente) ? ')' : '' !!}</div>
                @endif
                @if (!empty($telefono_cliente))
                    <div style="font-size:8pt;"><strong>Teléfono:</strong> {!! $telefono_cliente !!}</div>
                @endif
                <div style="font-size:8pt;"><strong>Monto:</strong> {!! $moneda['simbolo'] ?? '$' !!}{!! $monto !!}</div>
            </td>
            <td style="width:30%; text-align:right; vertical-align:top; color:#444">
                #{!! $num !!}
                <br>
                <span style="font-size:7pt">{!! $fecha !!}</span>
                <br>
                <span style="font-size:7pt">{!! $usuario ? ($usuario->persona ? (\App\Http\Controllers\PersonaController::concatenarNombresYDni($usuario->persona, true)) : $usuario->nombre) : '' !!}</span>
                <br>
                <span style="font-size:7pt">{!! $pagina !!}/{!! $total_paginas !!}</span>
            </td>
        </tr>
    </thead>
</table>