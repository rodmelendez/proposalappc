<?php

use App\Migracion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TablaUbicacion extends Migracion {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ubicacion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sub_departamento')->nullable();
            $table->integer('id_sucursal')->nullable();
            $table->string('nombre', 60);
            $table->smallInteger('tipo')->default(1);
            $this->idUsuariofechasYStatus($table);
        });

        //relaciones

        Schema::table('ubicacion', function($table) {
            $table->foreign('id_sub_departamento')->references('id')->on('sub_departamento')->onDelete('set null');
            $table->foreign('id_sucursal')->references('id')->on('sucursal')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('ubicacion');
    }

}