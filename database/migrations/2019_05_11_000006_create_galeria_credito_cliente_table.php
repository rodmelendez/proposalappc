<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateGaleriaCreditoClienteTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'galeria_credito_cliente';

    /**
     * Run the migrations.
     * @table galeria_credito_cliente
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 63);
            $table->string('dni', 31)->nullable();
            $table->smallInteger('tipo')->nullable();
            $table->string('negocio', 63)->nullable();
            $table->string('ruc', 31)->nullable();
            $table->string('direccion', 127)->nullable();
            $table->string('telefono', 127)->nullable();
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
