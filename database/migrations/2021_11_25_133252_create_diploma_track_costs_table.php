<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiplomaTrackCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diploma_track_costs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('diploma_track_id')->unsigned();
            $table->double('price',20,2)->default(0);
            $table->double('corporate',20,2)->default(0);
            $table->double('private',20,2)->default(0);
            $table->double('online',20,2)->default(0);
            $table->double('protocol',20,2)->default(0);
            $table->double('corporate_group',20,2)->default(0);
            $table->double('official',20,2)->default(0);
            $table->double('soft_copy_cd',20,2)->default(0);
            $table->double('soft_copy_flash_memory',20,2)->default(0);
            $table->double('hard_copy',20,2)->default(0);
            $table->double('lab_virtual',20,2)->default(0);
            $table->double('membership_price',20,2)->default(0);
            $table->double('application_price',20,2)->default(0);
            $table->double('exam_price',20,2)->default(0);
            $table->double('block_note',20,2)->default(0);
            $table->double('pen',20,2)->default(0);
            $table->double('training_kit',20,2)->default(0);

            $table->foreign('diploma_track_id')->references('id')->on('diploma_tracks')->onDelete('cascade');
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
        Schema::dropIfExists('diploma_track_costs');
    }
}
