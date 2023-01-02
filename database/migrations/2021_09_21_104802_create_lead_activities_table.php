<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subject_id')->unsigned()->nullable();
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->bigInteger('lead_id')->unsigned();
            $table->bigInteger('employee_id')->unsigned();
            $table->dateTime('close_date')->nullable();

            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
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
        Schema::dropIfExists('lead_activities');
    }
}
