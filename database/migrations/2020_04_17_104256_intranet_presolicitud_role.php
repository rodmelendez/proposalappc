<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IntranetPresolicitudRole extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('intranet_user_rol_presolicitud', function (Blueprint $table) {
         
            $table->bigIncrements('id');
          
            $table->integer('role'); // ,1 ejecutivo(solo subir documentos, imagenes y observaciones),2)supervisores crud + crear roles,3) comite, solo puede ver,  4) comite, todos los permisos menos borrar 
            $table->unsignedBigInteger('id_usuario')->nullable($value = true);
            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->unsignedBigInteger('id_presolicitud')->nullable($value = true);
            $table->foreign('id_presolicitud')->references('id')->on('intranet_presolicitud');
            $this->fechasYStatus($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intranet_user_rol_presolicitud');
    }
}
