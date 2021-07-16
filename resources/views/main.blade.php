<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{!! csrf_token() !!}">

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{!! asset('img/favicon/apple-touch-icon.png') !!}">
    <link rel="icon" type="image/png" sizes="32x32" href="{!! asset('img/favicon/favicon-32x32.png') !!}">
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('img/favicon/favicon-16x16.png') !!}">
    <link rel="manifest" href="{!! asset('img/favicon/site.webmanifest') !!}">
    <link rel="mask-icon" href="{!! asset('img/favicon/safari-pinned-tab.svg') !!}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="{!! asset('css/line-awesome.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/fonts.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/style.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/plugins.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/animate.min.css') !!}">

    <title>INTREZA</title>
</head>
<body>
    <div id="app" class="container-app">
        <app-main
                avatar_defecto="{!! asset('img/default-avatar.jpg') !!}"
                img_placeholder="{!! asset('img/img-placeholder.png') !!}"
                :urls="{
                    get: '{!! route('get') !!}',
                    post: '{!! route('post') !!}',
                    cerrar_sesion: '{!! route('cerrar_sesion') !!}',
                    uploads_img_dir: '{!! asset(config('app.uploads_img_dir')) . '/' !!}',
                    uploads_doc_dir: '{!! asset(config('app.uploads_doc_dir')) . '/' !!}',
                    public_dir: '{!! asset('/') !!}',
                }"
                :usuario='{!! json_encode($usuario) !!}'
                :persona='{!! json_encode($persona) !!}'
                :empresas='{!! json_encode($empresas) !!}'
                :opciones='{!! json_encode($opciones) !!}'
        >
        </app-main>
    </div>

    <script type="text/javascript" src="{!! asset('js/plugins.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/app.js') !!}"></script>
</body>
</html>