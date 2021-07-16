<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEmpresa extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_empresa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre')->nullable($value = false);
            $table->string('razon')->nullable($value = false)->unique();
            $table->string('ruc')->nullable($value = false)->unique();
            $table->unsignedBigInteger('id_cliente');
            $table->foreign('id_cliente')->references('id')->on('intranet_cliente');
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
        Schema::dropIfExists('intranet_empresa');
    }
}
