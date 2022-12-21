<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('commission_type_id')->unsigned();
            $table->string('name');
            $table->bigInteger('per_id')->unsigned();
            $table->double('amount',22,2)->default(0);
            $table->double('percentage',8,2)->default(0);

            $table->foreign('per_id')->references('id')->on('pers')->onDelete('cascade');
            $table->foreign('commission_type_id')->references('id')->on('commission_types')->onDelete('cascade');
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
        Schema::dropIfExists('commissions');
    }
}
