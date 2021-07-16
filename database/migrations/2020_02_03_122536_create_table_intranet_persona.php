<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIntranetPersona extends \App\Migracion
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
        public function up()
        {
            Schema::create('intranet_persona', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('primer_nombre')->nullable($value = false);
                $table->string('segundo_nombre')->nullable($value = true);
                $table->string('primer_apellido')->nullable($value = false);
                $table->string('segundo_apellido')->nullable($value = true);
                $table->string('pasaporte')->nullable($value = true);
                $table->string('dni')->nullable($value = true)->unique();
                $table->string('ruc')->nullable($value = true)->unique();
                $table->enum('genero',['Femenino','Masculino'])->nullable($value = true);
                $table->date('fecha_nacimiento',0)->nullable($value = false);
                $table->unsignedBigInteger('id_cliente');
                $table->foreign('id_cliente')->references('id')->on('intranet_cliente');
                $this->fechasYStatus($table);
            });
        }
    
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('intranet_persona');
        }
}
