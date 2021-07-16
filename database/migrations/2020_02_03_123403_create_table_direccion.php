<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDireccion extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intranet_direccion', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('id_cliente');
            $table->foreign('id_cliente')->references('id')->on('intranet_cliente');
            
            $table->unsignedBigInteger('id_barrio');
            $table->foreign('id_barrio')->references('id')->on('intranet_barrio');
            $table->text('descripcion');
            $table->string('pertenece');
            $table->string('coordenadas')->nullable($value = true);
            $table->enum('tipo_direccion',['Casa','Trabajo','Negocio','Sucursal']);
            //$table->double('latitud');
            //$table->double('longitud');

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
        Schema::dropIfExists('intranet_direccion');
    }
}
