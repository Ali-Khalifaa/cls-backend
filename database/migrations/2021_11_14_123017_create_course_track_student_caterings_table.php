<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseTrackStudentCateringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_track_student_caterings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_track_student_id')->unsigned();
            $table->bigInteger('course_track_catering_id')->unsigned();

            $table->foreign('course_track_student_id')->references('id')->on('course_track_students')->onDelete('cascade');
            $table->foreign('course_track_catering_id')->references('id')->on('course_track_caterings')->onDelete('cascade');
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
        Schema::dropIfExists('course_track_student_caterings');
    }
}
