<?php

use App\Migracion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TablaEmpleado extends Migracion {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('empleado', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_persona');
            $table->string('num_control', 63)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->string('grado', 63)->nullable();
            $table->string('tipo_cargo', 31)->nullable();
            $table->decimal('salario_actual', 12, 2)->nullable();
            $table->integer('id_empresa')->nullable();
            $this->idUsuariofechasYStatus($table);
        });

        Schema::create('empleado_empresa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_empleado');
            $table->integer('id_empresa');
        });

        //relaciones

        Schema::table('empleado', function($table) {
            $table->foreign('id_persona')->references('id')->on('persona')->onDelete('cascade');
        });

        Schema::table('empleado_empresa', function($table) {
            $table->foreign('id_empleado')->references('id')->on('empleado')->onDelete('cascade');
            $table->foreign('id_empresa')->references('id')->on('empresa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('empleado_empresa');
        Schema::dropIfExists('empleado');
    }

}