<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccompagnateursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accompagnateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nomArabe');
            $table->string('prenomArabe');
            $table->enum('sexe', array('0', '1'))->default('0');
            $table->string('telephoneTunisien');
            $table->string('telephoneEtranger') ->nullable();
            $table->string('image') ->nullable();
            $table->enum('etat', ['0','1'])->default('0');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('package_id') ->nullable();
            $table->foreign('user_id')
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
        Schema::dropIfExists('accompagnateurs');
    }
}
