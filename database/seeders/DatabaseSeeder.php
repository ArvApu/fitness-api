<?php

namespace Database\Seeders;

use App\Models\NewsEvent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
             UserSeeder::class,
             ExerciseSeeder::class,
             WorkoutSeeder::class,
             NewsEventSeeder::class,
         ]);
    }
}
