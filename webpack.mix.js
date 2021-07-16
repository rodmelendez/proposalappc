const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/style.scss', 'public/css').sourceMaps();

mix.styles([
    'public/css/fontawesome.css',
    'public/css/icons.css',
    'public/css/nunito.css', //'public/css/roboto.css',
], 'public/css/fonts.css');

mix.styles([
    'public/css/plugins/jquery.confirm.min.css',

    'public/css/plugins/dropify.min.css',
    'public/css/plugins/file-explore.css',
    //'public/css/plugins/dialog_fx.css',

    //'public/css/plugins/bootstrap-datepicker.min.css',
    'public/css/plugins/bootstrap-datetimepicker.min.css',

    'public/css/plugins/fullcalendar.min.css',
    'public/css/plugins/scheduler.min.css',
    'public/js/plugins/leaflet/leaflet.css',
], 'public/css/plugins.css');

mix.scripts([
    'public/js/plugins/jquery-3.3.1.min.js',
    'public/js/plugins/jquery-migrate-3.0.0.min.js',
    'public/js/plugins/mmenu.min.js',
    'public/js/plugins/tippy.all.min.js',
    //'public/js/plugins/simplebar.min.js',
    'public/js/plugins/bootstrap-slider.min.js',
    'public/js/plugins/bootstrap-select.min.js',
    'public/js/plugins/snackbar.js',
    'public/js/plugins/clipboard.min.js',
    'public/js/plugins/counterup.min.js',
    //'public/js/plugins/magnific-popup.min.js',
    'public/js/plugins/slick.min.js',
    'public/js/plugins/jquery.confirm.min.js',
    'public/js/plugins/chart.min.js',
    'public/js/plugins/moment-with-locales.min.js',

    //'public/js/plugins/bootstrap-datepicker.min.js',
    //'public/js/plugins/bootstrap-datepicker.es.min.js',
    'public/js/plugins/bootstrap-datetimepicker.min.js',
    'public/js/plugins/bootstrap-datetimepicker.es.js',

    /*'public/js/plugins/infobox.min.js',
    'public/js/plugins/maps.js',
    'public/js/plugins/markerclusterer.js',*/
    'public/js/plugins/select2.min.js',
    'public/js/plugins/select2.es.js',
    'public/js/plugins/dropify.min.js',
    'public/js/plugins/jscolor.js',
    'public/js/plugins/jquery-ui-sortable.min.js',
    'public/js/plugins/editorjs.min.js',
    //'public/js/plugins/dialog_fx.js',
    'public/js/plugins/fullcalendar.min.js',
    'public/js/plugins/fullcalendar.locale.js',
    'public/js/plugins/scheduler.min.js',
    'public/js/plugins/leaflet/leaflet.js',
    'public/js/plugins/custom.js',
    'public/js/funciones.js',
], 'public/js/plugins.js');