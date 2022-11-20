<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseTrackDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_track_days', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_track_id')->unsigned();
            $table->bigInteger('day_id')->unsigned();
            $table->string('day');

            $table->foreign('course_track_id')->references('id')->on('course_tracks')->onDelete('cascade');
            $table->foreign('day_id')->references('id')->on('days')->onDelete('cascade');
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
        Schema::dropIfExists('course_track_days');
    }
}
