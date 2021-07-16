<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoCreaditoDetalle extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intrenet_presolicitud_documento_detalle', function (Blueprint $table) { //son los detalles del documento
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_presolicitud_documento');
            $table->foreign('id_presolicitud_documento')->references('id')->on('intranet_presolicitud_documento');
            $table->text('observaciones')->default('ninguna');
            $table->integer('ancho')->nullable($value = true);
            $table->integer('alto')->nullable($value = true);
            $table->string('latitud')->nullable($value = true);
            $table->string('longitud')->nullable($value = true);
            $table->integer('kb')->nullable($value = true);
            $table->dateTime('fecha_captura',0)->nullable($value = true);
            $table->string('nombre_original')->nullable($value = true);
            $table->string('camara')->nullable($value = true);
            $table->unsignedBigInteger('id_usuario')->nullable($value = true);
            $table->foreign('id_usuario')->references('id')->on('usuario');
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
        Schema::dropIfExists('intrenet_presolicitud_documento_detalle');
    }
}
