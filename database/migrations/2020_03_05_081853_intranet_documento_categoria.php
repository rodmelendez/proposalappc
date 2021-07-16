<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\IntranetDocumentoCategoria as DocumentoCategoria;
class IntranetDocumentoCategoria extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_documento_categoria', function (Blueprint $table) { //esto en realidad son las carpetas
            $table->bigIncrements('id');
            $table->string('nombre',255);//nombre del documento
            $table->unsignedBigInteger('id_usuario')->nullable($value = true);
            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->unsignedBigInteger('id_documento_categoria')->nullable($value = true);
            $table->foreign('id_documento_categoria')->references('id')->on('intranet_documento_categoria');
            $this->fechasYStatus($table);
        });
        $EtapaPresolicitud = new DocumentoCategoria;
        $EtapaPresolicitud->nombre ='Presolicitud';
        $EtapaPresolicitud->save();
      
        $Analisis = new DocumentoCategoria;
        $Analisis->nombre ='Analisis Credito';
        $Analisis->save();
        $Supervision = new DocumentoCategoria;
        $Supervision->nombre ='Supervision Credito';
        $Supervision->save();
        $Comite = new DocumentoCategoria;
        $Comite->nombre ='Comite Credito';
        $Comite->save();
        $Desembolso = new DocumentoCategoria;
        $Desembolso->nombre ='Desembolso';
        $Desembolso->save();
        $Desembolso = new DocumentoCategoria;
        $Desembolso->nombre ='otros';
        $Desembolso->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intranet_documento_categoria'); //esto en realidad son las carpetas
    }
}
