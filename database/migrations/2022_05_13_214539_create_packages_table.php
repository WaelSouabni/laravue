<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('labelle');
            $table->longText('description');
            $table->integer('NombrePlace');
            $table->integer('NombrePlaceRestant');
            $table->integer('NombreAcc'); 
            $table->integer('NombreAccRestant');
            $table->float('prix',8,2);
            $table->date('dateDepart');
            $table->string('image')->nullable();
            $table->enum('etat', ['0','1'])->default('0');
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
        Schema::dropIfExists('packages');
    }
}
