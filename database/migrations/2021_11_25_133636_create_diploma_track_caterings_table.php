<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiplomaTrackCateringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diploma_track_caterings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('diploma_track_id')->unsigned();
            $table->bigInteger('catering_id')->unsigned();
            $table->double('catering_price',20,2)->default(0);
            $table->foreign('diploma_track_id')->references('id')->on('diploma_tracks')->onDelete('cascade');
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
        Schema::dropIfExists('diploma_track_caterings');
    }
}
