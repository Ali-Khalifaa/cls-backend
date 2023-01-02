<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiplomaTrackStudentCateringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diploma_track_student_caterings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('diploma_track_student_id')->unsigned();
            $table->bigInteger('diploma_track_catering_id')->unsigned();

            $table->foreign('diploma_track_student_id')->references('id')->on('diploma_track_students')->onDelete('cascade');
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
        Schema::dropIfExists('diploma_track_student_caterings');
    }
}
