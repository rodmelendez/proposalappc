<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateGaleriaCreditoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'galeria_credito';

    /**
     * Run the migrations.
     * @table galeria_credito
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_galeria_credito_cliente');
            $table->string('nombre', 63);
            $table->string('fecha', 63)->nullable();
            $table->decimal('monto', 11, 2)->nullable();
            $table->integer('id_moneda')->nullable();
            $table->string('moneda_iso', 4)->nullable();
            $table->string('moneda_simbolo', 4)->nullable();
            $this->idUsuariofechasYStatus($table);

            $table->index(["id_galeria_credito_cliente"], 'fk_galeria_credito_galeria_credito_cliente1_idx');


            $table->foreign('id_galeria_credito_cliente', 'fk_galeria_credito_galeria_credito_cliente1_idx')
                ->references('id')->on('galeria_credito_cliente')
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
