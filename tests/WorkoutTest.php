<?php

use App\Models\User;
use App\Models\Workout;
use Laravel\Lumen\Testing\DatabaseMigrations;

class WorkoutTest extends TestCase
{
    use DatabaseMigrations;

    private $resource = '/workouts';

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_get_all()
    {
        $user = User::factory()->create();

        $workouts = Workout::factory()
            ->count(10)
            ->for($user, 'author')
            ->create();

        $this->actingAs($user);

        $this->get($this->resource);

        $this->response->assertJson($workouts->toArray());
    }
}
