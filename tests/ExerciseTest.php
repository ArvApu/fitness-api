<?php

use App\Models\User;
use App\Models\Exercise;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ExerciseTest extends TestCase
{
    use DatabaseMigrations;

    private $resource = '/exercises';

    public function test_get_all()
    {
        $user = User::factory()->create();

        $exercises = Exercise::factory()
            ->count(10)
            ->for($user, 'author')
            ->create();

        $this->actingAs($user);

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $this->response->assertJson($exercises->toArray());
    }

    public function test_get_single()
    {
        $user = User::factory()->create();

        /** @var Exercise $exercise */
        $exercise = Exercise::factory()
            ->for($user, 'author')
            ->create();

        $this->actingAs($user);

        $this->get("$this->resource/$exercise->id");

        $this->response->assertStatus(200);
        $this->response->assertJson($exercise->toArray());
    }

    public function test_store()
    {
        $this->actingAs(User::factory()->create());

        $payload = [
            'name' => 'Test exercise',
            'description' => 'This is a test exercise',
            'is_private' => false,
        ];

        $this->post($this->resource, $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment($payload);

        $this->assertDatabaseHas((new Exercise)->getTable(), $payload);
    }

    public function test_update()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        /** @var Exercise $exercise */
        $exercise = Exercise::factory()
            ->for($user, 'author')
            ->create();

        $payload = [
            'name' => 'Test exercise',
            'description' => 'This is a test exercise',
            'is_private' => false,
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
        $user = User::factory()->create();

        /** @var Exercise $exercise */
        $exercise = Exercise::factory()
            ->for($user, 'author')
            ->create();

        $this->actingAs($user);

        $this->delete("$this->resource/$exercise->id");

        $this->response->assertStatus(204);
        $this->assertDatabaseMissing((new Exercise)->getTable(), $exercise->toArray());
    }
}
