<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructors', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('email_two')->nullable();
            $table->text('cls_rate');
            $table->string('pdf')->nullable();
            $table->string('img')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->boolean('has_account')->default(0);
            $table->boolean('active')->default(1);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('instructors');
    }
}
