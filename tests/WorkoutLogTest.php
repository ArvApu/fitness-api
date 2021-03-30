<?php

use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Laravel\Lumen\Testing\DatabaseMigrations;

class WorkoutLogTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/workouts/logs';

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
        $this->user = User::factory()->for($this->trainer, 'trainer')->create();
        $this->actingAs($this->user);
    }

    public function test_get_all()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for(User::factory()->trainer()->create(), 'author')
            ->create();

        $logs = WorkoutLog::factory()
            ->count(10)
            ->for($this->user, 'user')
            ->for($workout, 'workout')
            ->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'data' => $logs->toArray()
        ]);
    }

    public function test_get_single()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($this->trainer, 'author')
            ->create();

        /** @var WorkoutLog $log */
        $log = WorkoutLog::factory()
            ->for($this->user, 'user')
            ->for($workout, 'workout')
            ->create();

        $this->get("$this->resource/$log->id");

        $this->response->assertStatus(200);
        $this->response->assertJson($log->toArray());
    }

    public function test_store()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($this->trainer, 'author')
            ->create();

        $payload = [
            'workout_id' => $workout->id,
            'status' => 'interrupted',
            'difficulty' => 'hard'
        ];

        $this->post($this->resource, $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment($payload);

        $this->assertDatabaseHas((new WorkoutLog)->getTable(), $payload);
    }

    public function test_store_with_exercises_logging()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($this->trainer, 'author')
            ->create();

        /** @var Exercise $exercise */
        $exercise = Exercise::factory()
            ->for($this->trainer, 'author')
            ->create();

        $payload = [
            'workout_id' => $workout->id,
            'status' => 'completed',
            'difficulty' => 'hard',
            'exercise_logs' => [
                [
                    'exercise_id' => $exercise->id,
                    'measurement_value' => 10.5,
                    'sets_count' => 7,
                    'sets_done' => 7,
                ],
            ],
        ];

        $this->post($this->resource, $payload);

        $this->response->assertStatus(201);

        $this->response->assertJsonFragment([
            'workout_id' => $workout->id,
            'status' => 'completed',
            'difficulty' => 'hard',
        ]);

        $this->assertDatabaseHas((new WorkoutLog)->getTable(), [
            'workout_id' => $workout->id,
            'status' => 'completed',
            'difficulty' => 'hard',
        ]);

        $this->assertDatabaseHas((new ExerciseLog())->getTable(), [
            'exercise_id' => $exercise->id,
            'measurement_value' => 10.5,
            'sets_count' => 7,
            'sets_done' => 7,
        ]);
    }

    public function test_delete()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($this->trainer, 'author')
            ->create();

        /** @var WorkoutLog $log */
        $log = WorkoutLog::factory()
            ->for($this->user, 'user')
            ->for($workout, 'workout')
            ->create();

        $this->delete("$this->resource/$log->id");

        $this->response->assertNoContent();
        $this->assertDatabaseMissing((new WorkoutLog)->getTable(), $workout->toArray());
    }
}
