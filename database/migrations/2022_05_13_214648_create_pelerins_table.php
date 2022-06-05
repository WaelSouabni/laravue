<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePelerinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pelerins', function (Blueprint $table) {
            $table->id();
        
            $table->string('nomArabe');
            $table->string('prenomArabe');
            $table->date('dateNaissance');
            $table->enum('sexe', array('0', '1'))->default('0');
            $table->string('telephoneTunisien') ->nullable();
            $table->string('numeroDePassport') ->nullable();
            $table->date('dateExpiration') ->nullable();
            $table->date('dateEmission') ->nullable(); 
            $table->string('image') ->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('createur_id');
            $table->unsignedBigInteger('package_id') ->nullable();
            $table->enum('etat', ['1','0','2'])->default('1');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            
            $table->foreign('createur_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

                $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pelerins');
    }
}
