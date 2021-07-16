<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Contacto extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_contacto', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('id_cliente');
            $table->foreign('id_cliente')->references('id')->on('intranet_cliente');
            
            $table->enum('tipo',['Email','Telefono','Facebook','Instagram','Otro'])->nullable($value = true);
            $table->enum('pertenece',['Persona','Empresa'])->nullable($value = true);
            $table->text('descripcion')->nullable($value = true);
            $table->text('observacion')->nullable($value = true);
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
        Schema::dropIfExists('intranet_contacto');
    }
}
