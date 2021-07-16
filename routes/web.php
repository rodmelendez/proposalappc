<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//region Sesión no iniciada
Route::group(['middleware' => 'guest'], function() {

    //Formulario para iniciar sesión
    Route::get('/login', ['as' => 'login', 'uses' => 'Auth\SesionController@mostrarLogin']);

    //Procesa datos de inicio de sesión
    Route::post('/inicio-post', ['as' => 'inicio_post', 'uses' => 'Auth\SesionController@loginPost']);

});
//endregion

//region Sesión iniciada
Route::group(['middleware' => 'auth'], function() {

    //Principal
    Route::get('/', ['as' => 'main', 'uses' => 'AppController@mostrarMain']);

    //General
    Route::post('/post', ['as' => 'post', 'uses' => 'Controlador@post']);
    Route::get('/get', ['as' => 'get', 'uses' => 'Controlador@get']);
});

//avatar de usuario
Route::get('/avatar-usuario', ['as' => 'cargar_avatar_usuario', 'uses' => 'UserController@cargarAvatarGet']);

Route::get('/imagen', ['as' => 'imagen', 'uses' => 'ImagenController@output']);

Route::get('/cerrar-sesion', ['as' => 'cerrar_sesion', 'uses' => 'Auth\SesionController@cerrarSesion']);

Route::get('/testing', ['as' => 'testing', 'uses' => 'GaleriaCreditoController@cropImage']); //for testubg purposes only

Route::get('/fix_id_usuario', ['uses' => '\App\Http\Controllers\Ezadigital\SimicroCreditoController@vincularIdUsuarioIdEjecutivo']);