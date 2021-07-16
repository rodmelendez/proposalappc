<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'producto';

    /**
     * Run the migrations.
     * @table producto
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_tipo');
            $table->integer('id_marca');
            $table->integer('id_modelo');
            $table->integer('id_empresa')->nullable();
            $table->integer('id_departamento')->nullable();
            $table->integer('id_sub_departamento')->nullable();
            $table->integer('id_ubicacion')->nullable();
            $table->integer('id_categoria')->nullable();
            $table->string('nombre', 63);
            $table->string('codigo_sistema', 31)->nullable();
            $table->string('codigo_unico', 31)->nullable();
            $table->string('foto', 31)->nullable();
            $table->integer('cantidad')->default(1);
            $this->idUsuariofechasYStatus($table);

            $table->index(["id_modelo"], 'fk_producto_modelo1_idx');

            $table->index(["id_tipo"], 'fk_producto_tipo1_idx');

            $table->index(["id_marca"], 'fk_producto_marca1_idx');


            $table->foreign('id_tipo', 'fk_producto_tipo1_idx')
                ->references('id')->on('tipo')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('id_marca', 'fk_producto_marca1_idx')
                ->references('id')->on('marca')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('id_modelo', 'fk_producto_modelo1_idx')
                ->references('id')->on('modelo')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('id_categoria', 'fk_producto_categoria1_idx')
                ->references('id')->on('categoria')
                ->onDelete('set null')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
