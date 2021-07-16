<?php

use App\Migracion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TablaEmpresa extends Migracion {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('empresa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 60);
            $table->string('ubicacion', 255)->nullable();
            $table->string('telefono', 60)->nullable();
            $table->string('website', 60)->nullable();
            $table->string('logo', 60)->nullable();
            $table->string('color', 25)->nullable();
            $table->string('color_fondo', 25)->nullable();
            $table->smallInteger('tipo')->default(1);
            $this->idUsuariofechasYStatus($table);
        });

        Schema::create('sucursal', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_empresa');
            $table->string('nombre', 60);
            $table->string('ubicacion', 255)->nullable();
            $this->idUsuariofechasYStatus($table);
        });

        Schema::create('departamento', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sucursal');
            $table->string('nombre', 60);
            $table->smallInteger('tipo')->default(1);
            $this->idUsuariofechasYStatus($table);
        });

        Schema::create('sub_departamento', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_departamento');
            $table->string('nombre', 60);
            $table->smallInteger('tipo')->default(1);
            $this->idUsuariofechasYStatus($table);
        });

        //relaciones

        Schema::table('sucursal', function($table) {
            $table->foreign('id_empresa')->references('id')->on('empresa')->onDelete('cascade');
        });

        Schema::table('departamento', function($table) {
            $table->foreign('id_sucursal')->references('id')->on('sucursal')->onDelete('cascade');
        });

        Schema::table('sub_departamento', function($table) {
            $table->foreign('id_departamento')->references('id')->on('departamento')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sub_departamento');
        Schema::dropIfExists('departamento');
        Schema::dropIfExists('sucursal');
        Schema::dropIfExists('empresa');
    }

}