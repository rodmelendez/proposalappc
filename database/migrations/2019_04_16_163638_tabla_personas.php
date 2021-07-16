<?php

use App\Migracion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TablaPersonas extends Migracion {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('persona', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_usuario');
            $table->string('primer_nombre', 60);
            $table->string('segundo_nombre', 60)->nullable();
            $table->string('primer_apellido', 60)->nullable();
            $table->string('segundo_apellido', 60)->nullable();
            $table->string('dni', 60)->nullable();
            $table->string('nacionalidad', 60);
            $table->tinyInteger('sexo')->comment('0 - Femenino, 1 - Masculino');
            $table->string('direccion_domicilio', 255)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('foto', 60)->nullable();
            $this->fechasYStatus($table);
            //$table->unique(['dni']); //no puede ser único porque se está usando SoftDelete
        });

        Schema::create('contacto', function (Blueprint $table) {
            $table->integer('id_persona');
            $table->string('valor', 60);
            $table->tinyInteger('tipo')->comment('0 - Teléfono, 1 - Correo, 2 - ');
            $table->index(['id_persona']);
        });

        Schema::create('usuario_persona', function (Blueprint $table) {
            $table->integer('id_usuario');
            $table->integer('id_persona');
            $table->index(['id_persona', 'id_usuario']);
        });

        //relaciones

        Schema::table('contacto', function($table) {
            $table->foreign('id_persona')->references('id')->on('persona')->onDelete('cascade');
        });

        Schema::table('usuario_persona', function($table) {
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('cascade');
            $table->foreign('id_persona')->references('id')->on('persona')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('usuario_persona');
        Schema::dropIfExists('contacto');
        Schema::dropIfExists('persona');
    }
}
