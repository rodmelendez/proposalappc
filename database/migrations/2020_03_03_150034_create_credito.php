<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCredito  extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_presolicitud', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_simi')->nullable($value = true);
            $table->unsignedBigInteger('id_sucursal')->nullable(true);
            $table->foreign('id_sucursal')->references('id')->on('intranet_sucursal');
            $table->unsignedBigInteger('id_cliente');
            $table->foreign('id_cliente')->references('id')->on('intranet_cliente');
            $table->unsignedBigInteger('id_producto');
            $table->foreign('id_producto')->references('id')->on('intranet_presolicitud_producto');
            $table->unsignedBigInteger('id_usuario')->nullable($value = true);
            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->string('tasa_interes')->nullable(true);
            $table->string('forma_credito')->nullable(true);
            $table->text('descripcion')->nullable(true);
            $table->text('direccion')->nullable(true);
            $table->string('monto_solicitado');
            $table->string('monto_asignado')->nullable($value= true);
            $table->dateTime('fecha_asignacion',0)->nullable($value = true);
            $table->dateTime('fecha_solicitud',0);
            $table->integer('plazo_solicitado');
            $table->integer('plazo_asignado')->nullable($value = true);
            $table->integer('moneda');
            $table->unsignedBigInteger('estado_etapa')->default('1');
            $table->integer('estado_vida')->default('1');
            $table->string('estado_presolicitud')->default('VIGENTE');
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
        Schema::dropIfExists('intranet_presolicitud');
    }
}
