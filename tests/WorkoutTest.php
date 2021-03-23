<?php

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Laravel\Lumen\Testing\DatabaseMigrations;

class WorkoutTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/workouts';

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

        $this->user = User::factory()->trainer()->create();
        $this->actingAs($this->user);
    }

    public function test_get_all()
    {
        $workouts = Workout::factory()
            ->count(10)
            ->for($this->user, 'author')
            ->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'data' => $workouts->toArray()
        ]);
    }

    public function test_get_single()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($this->user, 'author')
            ->create();

        $this->get("$this->resource/$workout->id");

        $this->response->assertStatus(200);
        $this->response->assertJson($workout->toArray());
    }

    public function test_store()
    {
        $payload = [
            'name' => 'Test workout',
            'description' => 'This is a test workout',
            'type' => 'hiit',
        ];

        $this->post($this->resource, $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment($payload);

        $this->assertDatabaseHas((new Workout)->getTable(), $payload);
    }

    public function test_assign_exercises()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($this->user, 'author')
            ->create();

        /** @var Exercise $exercise1 */
        $exercise1 = Exercise::factory()->for($this->user, 'author')->create();
        /** @var Exercise $exercise2 */
        $exercise2 = Exercise::factory()->for($this->user, 'author')->create();

        $payload = [
            'exercises' => [
                ['id' => $exercise1->id, 'sets' => 5, 'reps' => 10, 'rest' => 30],
                ['id' => $exercise2->id, 'sets' => 2, 'reps' => 15, 'rest' => 45],
            ]
        ];

        $this->post("$this->resource/$workout->id/exercises", $payload);

        $this->response->assertNoContent();

        foreach ($payload['exercises'] as $exercise) {
            $this->assertDatabaseHas((new WorkoutExercise())->getTable(), [
                'workout_id' => $workout->id,
                'exercise_id' => $exercise['id'],
                'sets' => $exercise['sets'],
                'reps' => $exercise['reps'],
                'rest' => $exercise['rest'],
            ]);
        }
    }

    public function test_update()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($this->user, 'author')
            ->create();

        $payload = [
            'name' => 'Test workout',
            'description' => 'This is a test workout',
            'type' => 'hiit',
        ];

        $this->put("$this->resource/$workout->id", $payload);

        $this->response->assertStatus(200);
        $this->response->assertJsonFragment($payload);

        $table = (new Workout)->getTable();

        $this->assertDatabaseHas($table, $payload);
        $this->assertDatabaseMissing($table, $workout->toArray());
    }

    public function test_delete()
    {
        /** @var Workout $workout */
        $workout = Workout::factory()
            ->for($this->user, 'author')
            ->create();

        $this->delete("$this->resource/$workout->id");

        $this->response->assertNoContent();
        $this->assertDatabaseMissing((new Workout)->getTable(), $workout->toArray());
    }
}
