<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkoutsExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workouts_exercises', function (Blueprint $table) {
            $table->unsignedBigInteger('workout_id');
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedInteger('reps')->comment('Number of repetitions of exercise for this workout');
            $table->unsignedInteger('sets')->comment('Number of sets of repetitions of exercise for this workout');
            $table->unsignedInteger('rest')->comment('Time in seconds of rest between repetitions of exercise for this workout');

            $table->primary(['workout_id', 'exercise_id']);
            $table->foreign('workout_id')->references('id')->on('workouts')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workouts_exercises');
    }
}
