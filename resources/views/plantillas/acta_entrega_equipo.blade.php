<style type="text/css">
    table td {
        border: 1px solid #000;
        padding: 5px;
        vertical-align: top;
    }
</style>
<div class="hoja" style="max-width:21.59cm;">
    <table style="border-collapse:collapse; font-size:14px;">
        <tr>
            <td rowspan="2">
                <img src="{!! $logo !!}" alt="" style="max-height:120px;">
            </td>
            <td rowspan="2" colspan="2">
                <h1 style="font-size:18px; text-align:center;">ACTA DE ENTREGA DE EQUIPOS TECNOLÓGICOS</h1>
            </td>
            <td>
                <b>Ćodigo:</b> {!! $codigo !!}<br>
                <b>Versión:</b> 01 - {{ $fecha }}
            </td>
        </tr>
        <tr>
            <td>
                <b>Nº de doc</b>: <b>{!! $num_documento !!}</b>
            </td>
        </tr>
        <tr style="background-color:#0070C0;">
            <td colspan="4" style="color:#fff; text-align:center;">
                <b>DATOS COLABORADOR</b>
            </td>
        </tr>
        <tr>
            <td>
                <b>NOMBRE:</b><br>
                {!! $colaborador_nombre !!}
            </td>
            <td>
                <b>CARGO:</b><br>
                {!! $colaborador_cargo !!}
            </td>
            <td>
                <b>ÁREA/DEPARTAMENTO:</b><br>
                {!! $colaborador_departamento !!}
            </td>
            <td>
                <b>USUARIO - DOMINIO - EMAIL:</b><br>
                {!! $colaborador_correo !!}
            </td>
        </tr>
        <tr>
            <td>
                <b>CEDULA:</b><br>
                {!! $colaborador_dni !!}
            </td>
            <td>
                <b>TELF:</b><br>
                {!! $colaborador_telefono !!}
            </td>
            <td colspan="2">
                <b>DIRECCION:</b><br>
                {!! $colaborador_direccion !!}
            </td>
        </tr>
        <tr style="background-color:#0070C0;">
            <td colspan="4" style="color:#fff; text-align:center;">
                <b>DESCRIPCIÓN EQUIPO</b>
            </td>
        </tr>
        <tr>
            <td>
                <b>TIPO</b>
            </td>
            <td>
                <b>MODELO</b>
            </td>
            <td>
                <b>MARCA</b>
            </td>
            <td>
                <b>ATRIBUTOS</b>
            </td>
        </tr>
        @foreach ($productos as $producto)
            <tr>
                <td>
                    {!! $producto['tipo'] !!}
                </td>
                <td>
                    {!! $producto['modelo'] !!}
                </td>
                <td>
                    {!! $producto['marca'] !!}
                </td>
                <td>
                    @foreach ($producto['atributos'] as $atributo => $valor)
                        <div style="text-transform:uppercase;">{!! $atributo !!}: {!! $valor !!}</div>
                    @endforeach
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align:center;">
                <b>DESCRIPCION</b>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                {!! $descripcion !!}
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <div style="text-align:center;"><b>OBSERVACION:</b></div>
                {!! $observacion !!}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:justify;">
                Certifico que los elementos detallados en el presente documento, me han sido entregados en las cantidades descritas para mi cuidado y custodia con el propósito de cumplir con las tareas y asignaciones propias de mi cargo en la empresa, siendo estos de mi única y exclusiva responsabilidad. Me comprometo a usar correctamente los recursos, y solo para los fines establecidos. Todo daño físico causado por maltrato o por el uso inapropiado de los equipos asignados, el robo o pérdida de éstos es de mi única y exclusiva responsabilidad, por lo cual autorizo se descuente el valor correspondiente en el pago de nómina; en caso de finalizar mi contrato laboral me comprometo a realizar la devolución de la totalidad de los equipos asignados y autorizo el descuento de salarios, prestaciones sociales, vacaciones, indemnizaciones, bonificaciones. En caso de robo durante el ejercicio de las funciones laborales, me comprometo a presentar respaldo de la debida denuncia interpuesta en la Policía Nacional para que este no sea deducido.
            </td>
        </tr>
        <tr style="background-color:#0070C0;">
            <td colspan="4" style="color:#fff; text-align:center;">
                <b>ENTREGA DE EQUIPO</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding:0;">
                <table style="border:0; border-collapse:collapse; width:100%; font-size:14px;">
                    <tr style="background-color:#0070C0;">
                        <td style="width:33.3%; color:#fff; text-align:center; border-top:0; border-left:0;">
                            RECIBE
                        </td>
                        <td style="width:33.3%; color:#fff; text-align:center; border-top:0;">
                            ENTREGA
                        </td>
                        <td style="width:33.4%; color:#fff; text-align:center; border-top:0; border-right:0;">
                            AUTORIZA
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left:0;">
                            <b>NOMBRE:</b><br>
                            <b>{!! $nombre_receptor !!}</b>
                        </td>
                        <td>
                            <b>NOMBRE:</b><br>
                            <b>{!! $nombre_emisor !!}</b>
                        </td>
                        <td style="border-right:0;">
                            <b>NOMBRE:</b><br>
                            <b>{!! $nombre_autoriza !!}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left:0;">
                            <b>FIRMA:</b><br>
                            &nbsp;
                        </td>
                        <td>
                            <b>FIRMA:</b><br>
                            &nbsp;
                        </td>
                        <td style="border-right:0;">
                            <b>FIRMA:</b><br>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom:0; border-left:0;">
                            <b>FECHA:</b><br>
                            <b>04 de junio de 2019</b>
                        </td>
                        <td style="border-bottom:0;">
                            <b>FECHA:</b><br>
                            <b>04 de junio de 2019</b>
                        </td>
                        <td style="border-bottom:0; border-right:0;">
                            <b>FECHA:</b><br>
                            <b>04 de junio de 2019</b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>