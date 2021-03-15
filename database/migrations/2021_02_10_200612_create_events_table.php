<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendee_id');
            $table->unsignedBigInteger('organizer_id')->nullable();
            $table->unsignedBigInteger('workout_id')->nullable();
            $table->string('title', 100);
            $table->string('information');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->timestamps();

            $table->foreign('attendee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organizer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('workout_id')->references('id')->on('workouts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
