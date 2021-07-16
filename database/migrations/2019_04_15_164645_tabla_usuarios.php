<?php

use App\Migracion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TablaUsuarios extends Migracion {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 63);
            $table->string('contrasena', 63);
            $table->boolean('admin')->default(0);
            $table->string('api_token', 80)->nullable();
            $table->datetime('fecha_ultimo_ingreso')->nullable();
            $this->fechasYStatus($table);
        });

        $ahora = date('Y-m-d H:i:s');

        DB::table('usuario')->insert([
            'nombre' => 'admin',
            'contrasena' => bcrypt('Jamit!'),
            'fecha_creacion' => $ahora,
            'fecha_actualizacion' => $ahora,
            'admin' => 1
        ]);

        /* permisos */

        Schema::create('permiso', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 60);
            $table->string('descripcion', 255)->nullable();
            $table->string('categoria', 60)->nullable();
        });

        Schema::create('usuario_permiso', function (Blueprint $table) {
            $table->integer('id_usuario');
            $table->integer('id_permiso');
            $table->string('valor', 255)->nullable();
            $table->index(['id_permiso', 'id_usuario']);

        });

        /* roles */

        Schema::create('rol', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 40);
            $table->string('descripcion', 255)->nullable();
            $this->fechasYStatus($table);
        });

        Schema::create('usuario_rol', function (Blueprint $table) {
            $table->integer('id_usuario');
            $table->integer('id_rol');
            $table->index(['id_usuario', 'id_rol']);
        });

        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->integer('id_permiso');
            $table->integer('id_rol');
            $table->string('valor', 255)->nullable();
            $table->index(['id_permiso', 'id_rol']);
        });

        //relaciones

        Schema::table('usuario_permiso', function($table) {
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('cascade');
            $table->foreign('id_permiso')->references('id')->on('permiso')->onDelete('cascade');
        });

        Schema::table('usuario_rol', function($table) {
            $table->foreign('id_usuario')->references('id')->on('usuario')->onDelete('cascade');
            $table->foreign('id_rol')->references('id')->on('rol')->onDelete('cascade');
        });

        Schema::table('rol_permiso', function($table) {
            $table->foreign('id_permiso')->references('id')->on('permiso')->onDelete('cascade');
            $table->foreign('id_rol')->references('id')->on('rol')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('usuario_permiso');
        Schema::dropIfExists('usuario_rol');
        Schema::dropIfExists('rol_permiso');
        Schema::dropIfExists('permiso');
        Schema::dropIfExists('rol');
        Schema::dropIfExists('usuario');
    }

}