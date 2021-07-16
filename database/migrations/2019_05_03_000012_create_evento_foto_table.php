<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventoFotoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'evento_foto';

    /**
     * Run the migrations.
     * @table evento_foto
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_evento');
            $table->string('foto', 31)->nullable();
            $this->idUsuariofechasYStatus($table);

            $table->index(["id_evento"], 'fk_evento_foto_evento1_idx');


            $table->foreign('id_evento', 'fk_evento_foto_evento1_idx')
                ->references('id')->on('evento')
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
