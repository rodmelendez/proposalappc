<?php

use App\Migracion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TablaContrasena extends Migracion {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('credencial', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 63);
            $table->string('contrasena', 127);
            $table->string('usuario', 63)->nullable();
            $table->string('puerto', 31)->nullable();
            $table->string('dominio', 63)->nullable();
            $table->string('protocolo', 63)->nullable();
            $this->idUsuariofechasYStatus($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('credencial');
    }

}

class_alias('TablaContrasena', 'TablaCredencial');