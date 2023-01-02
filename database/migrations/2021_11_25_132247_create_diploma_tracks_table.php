<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiplomaTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diploma_tracks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lab_id')->unsigned()->nullable();
            $table->bigInteger('diploma_id')->unsigned()->unsigned();
            $table->bigInteger('instructor_id')->unsigned()->nullable();
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('vendor_id')->unsigned();
            $table->text('rate_per_hour')->nullable();
            $table->bigInteger('delivery_type_id')->unsigned()->nullable();

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('cancel')->default(0);
            $table->boolean('is_initial')->default(0);

            $table->foreign('diploma_id')->references('id')->on('diplomas')->onDelete('cascade');
            $table->foreign('delivery_type_id')->references('id')->on('delivery_types')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');


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
        Schema::dropIfExists('diploma_tracks');
    }
}
