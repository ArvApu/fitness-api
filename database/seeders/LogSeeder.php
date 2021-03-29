<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\Database\Seeder;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trainer = User::factory()->trainer()->create();
        $user = User::factory()->for($trainer, 'trainer')->create();

        $workouts = Workout::factory()
            ->count(3)
            ->for($trainer, 'author')
            ->create();

        $exercises =  Exercise::factory()
            ->count(5)
            ->for($trainer, 'author')
            ->create();

        $workoutLogs = [];

        foreach ($workouts as $workout) {
            $workoutLogs[] = WorkoutLog::factory()
                ->for($user, 'user')
                ->for($workout, 'workout')
                ->create();
        }

        foreach ($workoutLogs as $workoutLog) {
            foreach ($exercises as $exercise) {
                ExerciseLog::factory()
                    ->count(7)
                    ->for($exercise, 'exercise')
                    ->for($workoutLog, 'workoutLog')
                    ->for($user, 'user')
                    ->create();
            }
        }
    }
}
