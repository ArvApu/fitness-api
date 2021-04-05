<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['admin', 'trainer', 'user'])->default('user');
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->string('first_name', 64);
            $table->string('last_name', 64);
            $table->enum('gender', ['male', 'female']);
            $table->string('email', '128')->unique();
            $table->string('password');
            $table->date('birthday')->nullable();
            $table->string('about', 250)->nullable();
            $table->unsignedTinyInteger('experience')->nullable();
            $table->unsignedDouble('weight')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();

            $table->foreign('trainer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
