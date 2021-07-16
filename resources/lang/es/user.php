<?php
$saltar_return = true;
include(resource_path('lang/' . App::getLocale() . '/persona.php')); //<-- $persona_lang

$user_lang = array_merge($persona_lang, [
    'titulo' => 'Usuario',
    'titulo_plural' => 'Usuarios',

    'id' => 'ID',
    'nombre' => 'Usuario',
    'contrasena' => 'Contraseña',
    'contrasena_confirmar' => 'Confirmar Contraseña',
    'administrador' => 'Admin. del Sistema',
    'fecha_creacion' => 'Fecha de Registro',
    'rol' => 'Rol',
    'id_rol' => 'Rol',
    'id_persona' => 'Persona',
    'nombre_persona' => 'Nombre',

    'notificacion_usuario_admin' => 'El usuario administrador puede realizar todas las acciones.',

    'persona_ya_asignada' => 'La persona seleccionada ya pertenece a un usuario.',

    'cambiar_contrasena' => 'Cambiar contraseña',
    'contrasena_actual' => 'Contraseña actual',
    'contrasena_vacia' => 'Por favor ingrese la nueva contrasena.',
    'contrasena_confirmar_no_coincide' => 'Las contraseñas no coinciden.',
    'contrasena_actual_incorrecta' => 'La contraseña actual es incorrecta.',
    'contrasena_actualizada' => 'Contraseña actualizada.',

    'mi_cuenta' => 'Mi cuenta',
    'mis_libros' => 'Mis libros',
    'mis_examenes' => 'Mis exámenes',
    'mis_materias' => 'Mis materias',
    'mis_servicios' => 'Mis servicios',
    'cerrar_sesion' => 'Cerrar sesión',

    //validation

]);

return $user_lang;