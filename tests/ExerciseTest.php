<?php

use App\Models\User;
use App\Models\Exercise;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ExerciseTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/exercises';

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
        $exercises = Exercise::factory()
            ->count(10)
            ->for($this->user, 'author')
            ->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'data' => $exercises->toArray()
        ]);
    }

    public function test_get_single()
    {
        /** @var Exercise $exercise */
        $exercise = Exercise::factory()
            ->for($this->user, 'author')
            ->create();

        $this->get("$this->resource/$exercise->id");

        $this->response->assertStatus(200);
        $this->response->assertJson($exercise->toArray());
    }

    public function test_store()
    {
        $payload = [
            'name' => 'Test exercise',
            'description' => 'This is a test exercise',
        ];

        $this->post($this->resource, $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment($payload);

        $this->assertDatabaseHas((new Exercise)->getTable(), $payload);
    }

    public function test_update()
    {
        /** @var Exercise $exercise */
        $exercise = Exercise::factory()
            ->for($this->user, 'author')
            ->create();

        $payload = [
            'name' => 'Test exercise',
            'description' => 'This is a test exercise',
        ];

        $this->put("$this->resource/$exercise->id", $payload);

        $this->response->assertStatus(200);
        $this->response->assertJsonFragment($payload);

        $table = (new Exercise)->getTable();

        $this->assertDatabaseHas($table, $payload);
        $this->assertDatabaseMissing($table, $exercise->toArray());
    }

    public function test_delete()
    {
        /** @var Exercise $exercise */
        $exercise = Exercise::factory()
            ->for($this->user, 'author')
            ->create();

        $this->delete("$this->resource/$exercise->id");

        $this->response->assertNoContent();
        $this->assertDatabaseMissing((new Exercise)->getTable(), $exercise->toArray());
    }
}
