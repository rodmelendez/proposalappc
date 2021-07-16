@php
    $bg_color_1 = '#8cbd3a';
    $bg_color_2 = '#344f5d';
    $color_1 = '#344f5d';
    $color_2 = '#000';
@endphp
<style>
    @page {
        sheet-size: 5.5cm 8.5cm;
        margin: 0;
    }
</style>

<!-- PARTE ANTERIOR -->
<div class="carnet vertical" style="width:5.5cm; height:8.5cm; padding:0; overflow:hidden; font-family:'Avenir Next',Arial,sans-serif; color:{!! $color_2 !!}; position:relative">
    <div style="background-color:{!! $bg_color_1 !!}; width:100%; height:.3cm;"></div>

    <!-- logo -->
    <div class="contenedor-logo" style="padding-left:.5cm; padding-right:.5cm; text-align:center;">
        @if (!empty($logo))
            <img src="{!! route('imagen', ['src'=>basename($logo), 'w'=>400, 'h'=>400, 'c'=>0]) !!}" alt="" style="max-width:100%; max-height:1.47cm;">
        @endif
    </div>

    <!-- descripción carnet -->
    <table style="font-size:.3cm; font-weight:bold; line-height:1; width:100%;">
        <tr>
            <td style="text-align:center;" contenteditable="true">Carnet de Empleado</td>
        </tr>
    </table>

    <!-- foto -->
    <div class="contenedor-foto" style="padding-left:1.4cm; padding-right:1.4cm; height:2.8cm; text-align:center;">
        <img src="{!! !empty($foto) ? route('imagen', ['src'=>basename($foto), 'w'=>400, 'h'=>500, 'c'=>1]) : $url_foto !!}" alt="" style="max-height:2.8cm; border:1px solid #888;">
    </div>

    <!-- cargo -->
    <table style="font-size:.3cm; font-weight:bold; line-height:1; width:100%;">
        <tr>
            <td style="text-align:center;" contenteditable="true">{!! $cargo !!}</td>
        </tr>
    </table>

    <!-- detalles -->
    <table style="border:0; border-collapse:collapse; font-size:.3cm; line-height:1; margin:.1cm;">
        <tr>
            <td>Nombre:</td>
            <td style="height:.4cm"><span class="nombre">{!! $nombre !!}</span></td>
        </tr>
        <tr>
            <td style="white-space:nowrap;">Empleado #:</td>
            <td style="height:.4cm"><span class="num_carnet">{!! $num_carnet !!}</span></td>
        </tr>
        <tr>
            <td>Cédula:</td>
            <td style="height:.4cm"><span class="dni">{!! $dni !!}</span></td>
        </tr>
    </table>

    <table style="width:100%; text-align:center; color:#777; font-size:.27cm; line-height:1;">
        <tr>
            <td class="direccion" contenteditable="true" data-prop="direccion" style="height:.645cm;">{{ !empty($direccion) ? $direccion : '' }}</td>
        </tr>
        <tr>
            <td class="telefono" contenteditable="true" data-prop="telefono">{{ !empty($telefono) ? $telefono : '' }}</td>
        </tr>
        <tr>
            <td class="website" contenteditable="true" data-prop="website">{!! !empty($website) ? $website : '' !!}</td>
        </tr>
    </table>

    <div style="background-color:{!! $bg_color_2 !!}; width:100%; height:.38cm;"></div>
</div>