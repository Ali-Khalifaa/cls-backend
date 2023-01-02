<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subject_id')->unsigned()->nullable();
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('employee_id')->unsigned();
            $table->dateTime('close_date')->nullable();


            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::dropIfExists('company_activities');
    }
}
