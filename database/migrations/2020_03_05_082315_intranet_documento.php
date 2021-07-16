<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\IntranetDocumento as Documento;

class IntranetDocumento extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_documento', function (Blueprint $table) { //esta es la verdadera categoria del documento
            $table->bigIncrements('id');
            $table->string('nombre',255);//nombre del documento
            $table->text('descripcion')->nullable($value = true);
            $table->unsignedBigInteger('tipo');
            $table->boolean('opcional')->defaul('false');
            $table->integer('proceso')->default('1');
            $table->unsignedBigInteger('id_usuario')->nullable($value = true);
            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->unsignedBigInteger('id_documento_categoria')->nullable($value = true);
            $table->foreign('id_documento_categoria')->references('id')->on('intranet_documento_categoria');
            $this->fechasYStatus($table);
        });
       
        $documento= new Documento;
        $documento->nombre = "pdf_generico";
        $documento->tipo=2;
        $documento->opcional = true;
        $documento->id_documento_categoria= 6;
        $documento->save();
        $documento= new Documento;
        $documento->nombre = "foto_generico";
        $documento->tipo=1;
        $documento->opcional = true;
        $documento->id_documento_categoria= 6;
        $documento->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intranet_documento');

    }
}
