<?php

use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ExerciseLogTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/exercises/logs';

    /**
     * @var User
     */
    private $user;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_get_all()
    {
        $trainer = User::factory()->trainer()->create();

        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($trainer, 'author')
            ->create();

        /** @var Exercise $exercise */
        $exercise = Exercise::factory()
            ->for($trainer, 'author')
            ->create();

        /** @var WorkoutLog $wLog */
        $wLog = WorkoutLog::factory()
            ->for($this->user, 'user')
            ->for($workout, 'workout')
            ->create();

        $logs = ExerciseLog::factory()
            ->count(10)
            ->for($wLog, 'workoutLog')
            ->for($this->user, 'user')
            ->for($exercise, 'exercise')
            ->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'data' => $logs->toArray()
        ]);
    }

    public function test_get_single()
    {
        $trainer = User::factory()->trainer()->create();

        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($trainer, 'author')
            ->create();

        /** @var Exercise $exercise */
        $exercise = Exercise::factory()
            ->for($trainer, 'author')
            ->create();

        /** @var WorkoutLog $wLog */
        $wLog = WorkoutLog::factory()
            ->for($this->user, 'user')
            ->for($workout, 'workout')
            ->create();

        /** @var ExerciseLog $log */
        $log = ExerciseLog::factory()
            ->for($wLog, 'workoutLog')
            ->for($this->user, 'user')
            ->for($exercise, 'exercise')
            ->create();

        $this->get("$this->resource/$log->id");

        $this->response->assertStatus(200);
        $this->response->assertJson($log->toArray());
    }
}
