<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtributoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'atributo';

    /**
     * Run the migrations.
     * @table atributo
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 63);
            $table->smallInteger('tipo')->nullable();
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
