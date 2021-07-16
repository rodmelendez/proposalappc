@php
    $bg_color_1 = '#2f2f2f';
    $bg_color_2 = '#eea316';
    $color_1 = '#fff';
    $color_2 = '#000';
@endphp
<style>
    @page {
        sheet-size: 5.5cm 8.5cm;
        margin: 0;
    }
</style>

<!-- PARTE ANTERIOR -->
<div class="carnet vertical" style="height:8.5cm; width:5.5cm; padding:0; overflow:hidden; font-family:'Avenir Next',Arial,sans-serif; background-color:{!! $bg_color_2 !!}; color:{!! $color_2 !!}; position:relative">

    <div style="height:8.5cm; margin-bottom:-8.5cm; position:absolute; top:0; left:0; right:0; bottom:0; z-index:1;">
        <div class="sup" style="width:100%; height:4.35cm; background-color:{!! $bg_color_1 !!}; position:relative">

            {{--<div class="logo" style="width:3.3cm; height:3.3cm; background-image:url({!! $url_logo !!}); background-size:contain; background-repeat:no-repeat; position:absolute; top:.1cm; right:0; left:0; margin:0 auto">
            </div>--}}

            {{--<div class="cabecera" style="color:{!! $color_1 !!}; text-align:center; width:3.6cm; margin:0 auto; font-size:10px; padding-top:2cm;">
                Universidad Centroamericana de Ciencias Empresariales
            </div>--}}
        </div>

        {{--<div class="contenedor-foto" style="position:absolute; width:3.2cm; height:3.3cm; top:2.75cm; left:0; right:0; margin:0 auto; border:4px solid {!! $bg_color_2 !!}; background-color:#fff; background-image:url({!! $url_foto !!}); background-size:cover; background-position:top center;">
        </div>--}}

        <div class="inf" style="width:100%; height:4.35cm; padding-top:1.9cm; position:relative">
            @if (false)
                <!-- nombre -->
                <span class="nombre" style="font-size:0.7em; font-weight:bold; line-height:4.2mm; letter-spacing:-0.1mm; text-transform:uppercase; text-align:center; display:block">
                    {!! $grado_abreviatura !!} {!! $nombres !!}<br>{!! $apellidos !!}{{--Dra. María Mercedes Pacheco Solís--}}
                </span>

                <div style="text-align:center; font-size:11px;">{!! strtoupper($cargo) !!}</div>

                <!-- código de barras -->
                <div>
                    @if (!empty($codigo_barra))
                        <div class="codigo-barra" style="width:100%; height:.45cm; background-image:url('data:image/png;base64,{!! base64_encode($codigo_barra) !!}'); background-size:contain; background-repeat: no-repeat; background-position:center center;"></div>
                    @endif
                </div>

                <!-- num de carnet -->
                <div style="text-align:center">
                    <span class="numero-carnet" style="font-size:13px; letter-spacing:2px;">{!! $num_carnet !!}</span>
                </div>

                <div style="text-align:center; font-size:10px;">
                    Válido hasta: <span class="valido-hasta">{!! $valido_hasta !!}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- logo -->
    <div class="contenedor-logo" style="padding-left:.5cm; padding-right:.5cm; text-align:center;">
        <img src="{!! route('imagen', ['src'=>basename($logo), 'w'=>400, 'h'=>400, 'c'=>0]) !!}" alt="" style="width:100%">
    </div>
</div>