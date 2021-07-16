<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tipo';

    /**
     * Run the migrations.
     * @table tipo
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 63);
            $table->string('abreviatura', 31)->nullable();
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
