<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cliente extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_cliente', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->unsignedBigInteger('id_sucursal')->nullable($value = true);
            $table->unsignedBigInteger('id_simi')->nullable($value = true);
            $table->integer('id_tipo');
            $table->date('fecha_registro',0)->nullable($value = false);
            $table->string('nombre_simi')->nullable($value = true);
            $table->unsignedBigInteger('id_usuario')->nullable($value = true);
            $table->string('ruc')->nullable($value = true)->unique();
            $table->string('dni')->nullable($value = true)->unique();
            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->enum('estado',['activo','inactivo','eliminado'])->default('activo');
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
        Schema::dropIfExists('intranet_cliente');
    }
}
