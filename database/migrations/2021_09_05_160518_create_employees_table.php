<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('mobile');
            $table->string('mobile_two')->nullable();
            $table->string('email')->nullable();
            $table->string('email_two')->nullable();
            $table->bigInteger('job_id')->unsigned()->nullable();
            $table->bigInteger('department_id')->unsigned()->nullable();
            $table->string('pdf')->nullable();
            $table->date('hiring_date');
            $table->date('date_of_resignation')->nullable();
            $table->integer('insurance_number')->nullable();
            $table->integer('ID_number')->nullable();
            $table->date('birth_date');
            $table->bigInteger('military_id')->unsigned()->nullable();
            $table->enum('relation_status',['married','single'])->default('single');
            $table->string('name_of_company_insurance')->nullable();
            $table->double('salary',8,2)->nullable();
            $table->string('img');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->boolean('has_account')->default(0);
            $table->boolean('active')->default(1);
            $table->boolean('admin')->default(0);

            $table->foreign('military_id')->references('id')->on('militaries')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
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
        Schema::dropIfExists('employees');
    }
}
