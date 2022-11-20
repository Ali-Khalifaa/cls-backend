<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiplomaCateringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diploma_caterings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('diploma_id')->unsigned();
            $table->bigInteger('catering_id')->unsigned();
            $table->foreign('diploma_id')->references('id')->on('diplomas')->onDelete('cascade');
            $table->foreign('catering_id')->references('id')->on('caterings')->onDelete('cascade');
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
        Schema::dropIfExists('diploma_caterings');
    }
}
