<?php

use App\Migracion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TablaInventario extends Migracion {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('inventario', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 63)->nullable();
            $table->string('tipo', 63)->nullable();
            $table->string('marca', 63)->nullable();
            $table->string('modelo', 63)->nullable();
            $table->string('serie', 63)->nullable();
            $table->string('color', 63)->nullable();
            $table->string('estado', 63)->nullable();
            $table->string('responsable', 63)->nullable();
            $table->string('ubicacion', 63)->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('foto', 60)->nullable();
            $this->idUsuariofechasYStatus($table);
        });

        //relaciones

        /*Schema::table('inventario', function($table) {
            $table->foreign('id_matricula_materia')->references('id')->on('matricula_materia')->onDelete('cascade');
            $table->foreign('id_salon')->references('id')->on('salon')->onDelete('cascade');
            $table->foreign('id_profesor')->references('id')->on('profesor')->onDelete('set null');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('inventario');
    }

}