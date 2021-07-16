<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register API routes for your application. These
// | routes are loaded by the RouteServiceProvider within a group which
// | is assigned the "api" middleware group. Enjoy building your API!
// |
// */
// /*FOR TESTING ONLY*/
// Route::get('lista_prueba', 'ApiController@listaTestGet');
// /*END FOR TESTING ONLY*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// //iniciar sesión
// Route::post('usuario', 'ApiController@iniciarSesionPost');
// Route::get('_usuario', 'ApiController@iniciarSesionPost'); //for testing

Route::post('registrar_imei_usuario', 'ApiController@registrarImeiSerialPost');

// //empresas y empleados
// {
// 	Route::get('empresas', 'ApiController@listadoEmpresas');
// 	Route::get('empleados', 'ApiController@listadoEmpleados');
// }

// {
//     Route::get('intranet_persona','ApiController@intranetPersonaGet');
// }

// //documentos de créditos
// {
//     //listado de documentos de créditos
//     Route::get('listado_documentos_creditos', 'ApiController@listadoGaleriaCreditoGet');

//     //registrar documento de crédito
//     Route::post('registrar_documento_credito', 'ApiController@registrarGaleriaCreditoPost');

//     //registrar foto de documento de crédito
//     Route::post('registrar_documento_credito_fotos', 'ApiController@registrarGaleriaCreditoFotosPost');

//     //registrar ubicación del usuario
        Route::post('registrar_ubicacion_usuario', 'ApiController@registrarUbicacionUsuarioPost');

//     //cargar ubicaciones del usuario
//     Route::get('cargar_ubicaciones_usuario/{token?}', 'ApiController@cargarUbicacionesUsuarioGet');
// }

//ezadigital
{
    //simicro crédito
    Route::get('cartera_oficial/{token?}', 'ApiController@carteraOficialGet');
    Route::get('cartera_oficial_detalle/{token?}', 'ApiController@carteraOficialDetalleGet');
    Route::get('cartera_oficial_listado/{token?}', 'ApiController@carteraOficialListaGet');
    Route::get('cartera_oficial_sucursal/{token?}', 'ApiController@carteraOficialSucursalGet');
    Route::get('metas/{token?}', 'ApiController@metasUsuarioGet');
}

Route::post('usuario', 'ApiController@iniciarSesionPost');

//empresas y empleados
{
	Route::get('empresas', 'ApiController@listadoEmpresas');
	Route::get('empleados', 'ApiController@listadoEmpleados');
}

{
    Route::get('intranet_persona','EzaApiController@intranetPersonaGet');
}

//documentos de créditos
{
    //listado de documentos de créditos
    Route::get('listado_documentos_creditos', 'ApiController@listadoGaleriaCreditoGet');

    //registrar documento de crédito
    Route::post('registrar_documento_credito', 'ApiController@registrarGaleriaCreditoPost');

    //registrar foto de documento de crédito
    Route::post('registrar_documento_credito_fotos', 'ApiController@registrarGaleriaCreditoFotosPost');

    //registrar ubicación del usuario
    Route::post('registrar_ubicacion_usuario', 'ApiController@registrarUbicacionUsuarioPost');

    //cargar ubicaciones del usuario
    Route::get('cargar_ubicaciones_usuario/{token?}', 'ApiController@cargarUbicacionesUsuarioGet');
}

//ezadigital
{
    //simicro crédito
    Route::get('cartera_oficial/{token?}', 'ApiController@carteraOficialGet');
    Route::get('cartera_oficial_detalle/{token?}', 'ApiController@carteraOficialDetalleGet');
    Route::get('simular_credito', 'ApiController@simularCreditoGet');
    Route::get('obtener_usuarios','ApiController@obtenerUsuarios');

}


    //General
  {
    Route::get('catalogos', ['as' => 'catalogos', 'uses' => 'EzaApiController@catalogos']);
    Route::get('sub-carpeta', ['as' => 'sub-carpeta', 'uses' => 'EzaApiController@cargarSubCarpeta']);

    Route::get('clientes', ['as' => 'clientes', 'uses' => 'EzaApiController@clientes']);
    Route::get('cliente/{id}', ['as' => 'cliente', 'uses' => 'EzaApiController@cliente']);
    Route::post('cliente', ['as' => 'clientes', 'uses' => 'EzaApiController@clientePost']);
    Route::put('clienteupdate/{id}',['as' => 'clienteupdate', 'uses' => 'EzaApiController@clientePut']);
    Route::delete('clientedelete/{id}',['as' => 'clientedelete', 'uses' => 'EzaApiController@clienteDelete']);

    Route::get('presolicitud/{token?}', ['as' => 'presolicitud', 'uses' => 'EzaApiController@presolicitud']);
    Route::get('presolicitudes/{token?}', ['as' => 'presolicitudes', 'uses' => 'EzaApiController@presolicitudes']);
    Route::post('presolicitud/{token?}',['as' => 'presolicitud', 'uses' => 'EzaApiController@presolicitudPost']);
    Route::put('presolicitudupdate/{id}',['as' => 'presolicitudupdate', 'uses' => 'EzaApiController@presolicitudPut']);
    Route::delete('presolicituddelete/{token?}',['as' => 'presolicituddelete', 'uses' => 'EzaApiController@presolicitudDelete']);

    Route::post('direccion/{token?}',['as' => 'direccion', 'uses' => 'EzaApiController@direccionPost']);
    Route::put('direccionupdate/{id}',['as' => 'direccionupdate', 'uses' => 'EzaApiController@direccionPut']);

    Route::post('contacto/{token?}',['as' => 'contacto', 'uses' => 'EzaApiController@contactoPost']);
    Route::put('contactoupdate/{id}',['as' => 'contactoupdate', 'uses' => 'EzaApiController@contactoPut']);

  }
