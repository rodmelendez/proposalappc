<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoCreadito extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_presolicitud_documento', function (Blueprint $table) { // este almacena el documento
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_presolicitud');
            $table->foreign('id_presolicitud')->references('id')->on('intranet_presolicitud');
            $table->unsignedBigInteger('id_documento')->nullable($value = true); 
            $table->foreign('id_documento')->references('id')->on('intranet_documento');
            $table->unsignedBigInteger('id_carpeta'); 
            $table->foreign('id_carpeta')->references('id')->on('intranet_documento_categoria');
            $table->unsignedBigInteger('id_subcarpeta')->nullable($value = true); 
            $table->foreign('id_subcarpeta')->references('id')->on('intranet_documento_categoria');
            $table->unsignedBigInteger('tipo');//1 imagenes, 2 pdf;
            $table->date('fecha');//fecha de registro
            $table->date('fecha_vencimiento')->nullable($value = true);//fecha de vencimiento del documento
            $table->date('fecha_entrega')->nullable($value = true);
            $table->string('nombre',255);//nombre del documento
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
        Schema::dropIfExists('intranet_presolicitud_documento');
    }
}
