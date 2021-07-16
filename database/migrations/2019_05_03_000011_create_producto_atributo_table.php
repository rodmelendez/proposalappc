<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoAtributoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'producto_atributo';

    /**
     * Run the migrations.
     * @table producto_atributo
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->integer('id_atributo');
            $table->integer('id_producto');
            $table->string('valor', 63)->nullable();
            $this->idUsuariofechasYStatus($table);

            $table->index(["id_atributo"], 'fk_producto_atributo_atributo1_idx');

            $table->index(["id_producto"], 'fk_producto_atributo_producto1_idx');


            $table->foreign('id_atributo', 'fk_producto_atributo_atributo1_idx')
                ->references('id')->on('atributo')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('id_producto', 'fk_producto_atributo_producto1_idx')
                ->references('id')->on('producto')
                ->onDelete('cascade')
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
