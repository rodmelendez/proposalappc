<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientosPresolicitud extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_movimiento_presolicitud', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_usuario')->nullable($value = true);
            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->unsignedBigInteger('id_etapa_presolicitud');
            $table->foreign('id_etapa_presolicitud')->references('id')->on('intranet_etapa_presolicitud');
            $table->string('descripcion',250)->nullable($value = true );
            $table->dateTime('fecha',0);
            $table->string('movimiento',250)->default('creacion-credito');
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
        Schema::dropIfExists('movimientos_presolicitud');
    }
}
