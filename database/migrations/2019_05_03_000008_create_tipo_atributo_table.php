<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoAtributoTable extends \App\Migracion
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tipo_atributo';

    /**
     * Run the migrations.
     * @table tipo_atributo
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->integer('id_tipo');
            $table->integer('id_atributo');
            $this->idUsuariofechasYStatus($table);

            $table->index(["id_atributo"], 'fk_tipo_atributo_atributo1_idx');

            $table->index(["id_tipo"], 'fk_tipo_atributo_tipo_idx');


            $table->foreign('id_tipo', 'fk_tipo_atributo_tipo_idx')
                ->references('id')->on('tipo')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('id_atributo', 'fk_tipo_atributo_atributo1_idx')
                ->references('id')->on('atributo')
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
