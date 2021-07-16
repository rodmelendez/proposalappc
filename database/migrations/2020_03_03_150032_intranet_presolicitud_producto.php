<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IntranetPresolicitudProducto extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_presolicitud_producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_usuario')->nullable($value = true);
            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->string('nombre',250);
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
        Schema::dropIfExists('intranet_presolicitud_producto');
    }
}
