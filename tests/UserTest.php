<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = 'users';

    public function test_get_all_for_admin()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        User::factory()
            ->count(10)
            ->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
    }

    public function test_get_all_for_trainer()
    {
        $user = User::factory()->trainer()->create();
        $this->actingAs($user);

        $u1 = User::factory()->for($user,'trainer')->create();
        $u2 = User::factory()->for($user,'trainer')->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $result = json_decode($this->response->getContent());
        $this->assertEquals($u1->id, $result->data[0]->id);
        $this->assertEquals($u2->id, $result->data[1]->id);
    }

    public function test_get_all_for_user()
    {
        $trainer = User::factory()->trainer()->create();
        $user = User::factory()->for($trainer,'trainer')->create();
        $this->actingAs($user);

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $result = json_decode($this->response->getContent());
        $this->assertEquals($trainer->id, $result->data[0]->id);
    }
}
