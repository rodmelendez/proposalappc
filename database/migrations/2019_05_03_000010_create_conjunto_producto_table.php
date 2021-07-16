<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConjuntoProductoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'conjunto_producto';

    /**
     * Run the migrations.
     * @table conjunto_producto
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_conjunto');
            $table->integer('id_producto');
            $this->idUsuariofechasYStatus($table);

            $table->index(["id_conjunto"], 'fk_conjunto_producto_conjunto1_idx');

            $table->index(["id_producto"], 'fk_conjunto_producto_producto1_idx');


            $table->foreign('id_conjunto', 'fk_conjunto_producto_conjunto1_idx')
                ->references('id')->on('conjunto')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('id_producto', 'fk_conjunto_producto_producto1_idx')
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
