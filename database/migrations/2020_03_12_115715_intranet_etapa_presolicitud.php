<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IntranetEtapaPresolicitud extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_etapa_presolicitud', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_presolicitud');
            $table->foreign('id_presolicitud')->references('id')->on('intranet_presolicitud');
            $table->unsignedBigInteger('etapa')->default('1');
            $table->decimal('duracion',8,4)->default(0);
            $table->unsignedBigInteger('primerChek')->default('0');
            $table->unsignedBigInteger('segundoChek')->default('0');
            $table->dateTime('fecha_registro',0);
            $table->unsignedBigInteger('estatus');
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
        Schema::dropIfExists('intranet_etapa_presolicitud');
    }
}
