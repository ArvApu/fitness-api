<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Create trainers */
        $t1 = User::factory()->trainer()->create();
        $t2 = User::factory()->trainer()->create();

        /* Create client users for first trainer */
        User::factory()->count(10)->for($t1, 'trainer')->create();

        /* Create client users for first trainer */
        User::factory()->count(10)->for($t2, 'trainer')->create();
    }
}
