<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'evento';

    /**
     * Run the migrations.
     * @table evento
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_producto');
            //$table->smallInteger('status')->nullable();
            $table->string('observaciones')->nullable();
            $this->idUsuariofechasYStatus($table);

            $table->index(["id_producto"], 'fk_evento_producto1_idx');


            $table->foreign('id_producto', 'fk_evento_producto1_idx')
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
