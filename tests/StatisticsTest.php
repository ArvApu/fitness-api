<?php

use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\UserLog;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class StatisticsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/statistics';

    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $trainer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->trainer = User::factory()->trainer()->create();
        $this->user = User::factory()->for($this->trainer,'trainer')->create();
        $this->actingAs($this->user);
    }

    public function test_get_workouts_statistics()
    {
        WorkoutLog::factory()->for($this->user, 'user')->count(3)->missed()->create();
        WorkoutLog::factory()->for($this->user, 'user')->count(1)->interrupted()->create();
        WorkoutLog::factory()->for($this->user, 'user')->count(10)->completed()->create();

        $this->get("$this->resource/workouts");

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'missed' => 3,
            'interrupted' => 1,
            'completed' => 10
        ]);
    }

    public function test_get_workout_statistics()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()->for($this->trainer, 'author')->create();

        WorkoutLog::factory()->for($this->user, 'user')->for($workout, 'workout')
            ->count(3)->missed()->create();
        WorkoutLog::factory()->for($this->user, 'user')->for($workout, 'workout')
            ->count(1)->interrupted()->create();
        WorkoutLog::factory()->for($this->user, 'user')->for($workout, 'workout')
            ->count(2)->completed()->create();

        // This workout and it's log should not impact results
        $otherWorkout = Workout::factory()->for($this->trainer, 'author')->create();
        WorkoutLog::factory()->for($this->user, 'user')->for($otherWorkout, 'workout')->count(10)->completed()->create();

        $this->get("$this->resource/workouts/$workout->id");

        $this->response->assertStatus(200);

        $this->response->assertJson([
            'missed' => 3,
            'interrupted' => 1,
            'completed' => 2
        ]);
    }

    public function test_get_exercises_statistics()
    {
        $workoutLog = WorkoutLog::factory()->for($this->user, 'user')->create();

        ExerciseLog::factory()->for($this->user, 'user')->for($workoutLog, 'workoutLog')
            ->create(['sets_count' => 15, 'sets_done' => 15,]);
        ExerciseLog::factory()->for($this->user, 'user')->for($workoutLog, 'workoutLog')
            ->create(['sets_count' => 5, 'sets_done' => 5,]);
        ExerciseLog::factory()->for($this->user, 'user')->for($workoutLog, 'workoutLog')
            ->create(['sets_count' => 3, 'sets_done' => 3,]);

        $this->get("$this->resource/exercises");

        $this->response->assertStatus(200);

        $this->response->assertJson([
            'total_sets_done' => 23,
        ]);
    }

    public function test_get_exercise_statistics()
    {
        $timestamp = Carbon::now();

        /** @var Exercise $exercise */
        $exercise = Exercise::factory()->for($this->trainer, 'author')->create();
        $workoutLog = WorkoutLog::factory()->for($this->user, 'user')->create();

        ExerciseLog::factory()->for($this->user, 'user')->for($workoutLog, 'workoutLog')
            ->for($exercise, 'exercise')->create(['measurement_value' => 15, 'created_at' => $timestamp]);
        ExerciseLog::factory()->for($this->user, 'user')->for($workoutLog, 'workoutLog')
            ->for($exercise, 'exercise')->create(['measurement_value' => 25, 'created_at' => $timestamp]);
        ExerciseLog::factory()->for($this->user, 'user')->for($workoutLog, 'workoutLog')
            ->for($exercise, 'exercise')->create(['measurement_value' => 30.5, 'created_at' => $timestamp]);

        $this->get("$this->resource/exercises/$exercise->id");

        $this->response->assertStatus(200);

        $this->response->assertJson([
            'measurement_unit' => $exercise->measurement,
            'measurement_values' => [
                [
                    'measurement_value' => 15,
                    'created_at' => $timestamp,
                ],
                [
                    'measurement_value' => 25,
                    'created_at' => $timestamp,
                ],
                [
                    'measurement_value' => 30.5,
                    'created_at' => $timestamp,
                ],
            ],
        ]);
    }

    public function test_get_weight_statistics()
    {
        $timestamp = Carbon::now();

        UserLog::factory()->for($this->user, 'user')->create(['weight' => 90, 'created_at' => $timestamp]);
        UserLog::factory()->for($this->user, 'user')->create(['weight' => 89.5, 'created_at' => $timestamp]);
        UserLog::factory()->for($this->user, 'user')->create(['weight' => 88, 'created_at' => $timestamp]);

        $this->get("$this->resource/users/weight");

        $this->response->assertStatus(200);

        $this->response->assertJson([
            'data' => [
                [
                    'weight' => 90,
                    'created_at' => $timestamp,
                ],
                [
                    'weight' => 89.5,
                    'created_at' => $timestamp,
                ],
                [
                    'weight' => 88,
                    'created_at' => $timestamp,
                ],
            ],
        ]);
    }
}
