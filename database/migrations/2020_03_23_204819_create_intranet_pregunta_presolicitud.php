<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntranetPreguntaPresolicitud extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_pregunta_presolicitud', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_presolicitud')->nullable($value = true);
            $table->foreign('id_presolicitud')->references('id')->on('intranet_presolicitud');
            $table->unsignedBigInteger('id_usuario')->nullable($value = true);
            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->string('pregunta',250);
            $table->enum('estatus',['0','1'])->default('0');
            $table->date('fecha');
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
        Schema::dropIfExists('intranet_pregunta_presolicitud');
    }
}
