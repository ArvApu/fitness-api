<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddInitialAdminUserToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('users')->insert([
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => env('ADMIN_USER_EMAIL', 'admin@fake.mail'),
            'password' => Hash::make(env('ADMIN_USER_PASSWORD', 'admin')),
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')->where(
            'email','=',env('ADMIN_USER_EMAIL', 'admin@fake.mail')
        )->delete();
    }
}
