<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKardexTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'kardex';

    /**
     * Run the migrations.
     * @table kardex
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('tipo')->comment('0: Salida, 1: Entrada');
            $table->integer('id_producto');
            $table->integer('id_conjunto')->nullable();
            $this->idUsuariofechasYStatus($table);
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
