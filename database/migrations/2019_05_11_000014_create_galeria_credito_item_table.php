<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateGaleriaCreditoItemTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'galeria_credito_item';

    /**
     * Run the migrations.
     * @table galeria_credito_item
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_galeria_credito');
            $table->string('nombre', 63)->nullable();
            $table->smallInteger('tipo')->nullable();
            $table->string('foto', 31)->nullable();
            $table->date('fecha')->nullable();
            $table->tinyInteger('visible')->default('1');
            $table->string('observaciones')->nullable();
            $table->integer('ancho')->nullable();
            $table->integer('alto')->nullable();
            $table->string('latitud', 31)->nullable();
            $table->string('longitud', 31)->nullable();
            $table->integer('kb')->nullable();
            $table->string('iso', 31)->nullable();
            $table->string('apertura', 31)->nullable();
            $table->dateTime('fecha_captura')->nullable();
            $table->string('nombre_original', 63)->nullable();
            $table->string('camara', 31)->nullable();
            $table->smallInteger('indice')->nullable();
            $this->idUsuariofechasYStatus($table);

            $table->index(["id_galeria_credito"], 'fk_galeria_credito_item_galeria_credito1_idx');


            $table->foreign('id_galeria_credito', 'fk_galeria_credito_item_galeria_credito1_idx')
                ->references('id')->on('galeria_credito')
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
