<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkoutDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workout_days', function (Blueprint $table) {
            $table->unsignedBigInteger('workout_id');
            $table->unsignedBigInteger('day_id');

            $table->primary(['workout_id', 'day_id']);
            $table->foreign('workout_id')->references('id')->on('workouts')->onDelete('cascade');
            $table->foreign('day_id')->references('id')->on('days')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workout_days');
    }
}
