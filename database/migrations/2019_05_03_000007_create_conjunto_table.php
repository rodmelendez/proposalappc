<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConjuntoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'conjunto';

    /**
     * Run the migrations.
     * @table conjunto
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_tipo_conjunto');
            $table->string('nombre', 63)->nullable();
            $table->string('abreviatura', 31)->nullable();
            $this->idUsuariofechasYStatus($table);

            $table->index(["id_tipo_conjunto"], 'fk_conjunto_tipo_conjunto1_idx');


            $table->foreign('id_tipo_conjunto', 'fk_conjunto_tipo_conjunto1_idx')
                ->references('id')->on('tipo_conjunto')
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
