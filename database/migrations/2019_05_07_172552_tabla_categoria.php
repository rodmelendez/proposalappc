<?php

use App\Migracion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TablaCategoria extends Migracion {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('categoria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 63);
            $table->string('abreviatura', 31)->nullable();
            $this->idUsuariofechasYStatus($table);
        });

        //relaciones

        /*Schema::table('categoria', function($table) {
            $table->foreign('id_matricula_materia')->references('id')->on('matricula_materia')->onDelete('cascade');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('categoria');
    }

}