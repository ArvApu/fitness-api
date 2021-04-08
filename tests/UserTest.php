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

    public function test_get_all_filtered_by_q()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $users = User::factory()
            ->count(10)
            ->create();

        $this->get("$this->resource?q={$users->first()->first_name}");

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'data' => [$users->first()->toArray()]
        ]);
    }

    public function test_get_all_for_user()
    {
        $trainer = User::factory()->trainer()->create();
        $user = User::factory()->for($trainer,'trainer')->create();
        $this->actingAs($user);

        $this->get($this->resource);

        $this->response->assertForbidden();
    }

    public function test_destroy_user()
    {
        $trainer = User::factory()->trainer()->create();
        $user = User::factory()->for($trainer,'trainer')->create();
        $this->actingAs($trainer);

        $this->delete("$this->resource/$user->id");

        $this->response->assertNoContent();
    }

    public function test_fail_to_destroy_not_trained_user()
    {
        $evilTrainer = User::factory()->trainer()->create();
        $trainer = User::factory()->trainer()->create();
        $user = User::factory()->for($trainer,'trainer')->create();
        $this->actingAs($evilTrainer);

        $this->delete("$this->resource/$user->id");

        $this->response->assertStatus(400);
    }

    public function test_update_user()
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->for($admin,'trainer')->create();
        $this->actingAs($admin);

        $this->put("$this->resource/$user->id", ['email' => 'fake-test-mail@mail.com']);

        $this->response->assertStatus(200);
        $this->assertDatabaseMissing((new User())->getTable(), ['email' => $user->email]);
        $this->assertDatabaseHas((new User())->getTable(), ['email' => 'fake-test-mail@mail.com']);
    }

    public function test_fail_to_update_user_for_not_admin()
    {
        $trainer = User::factory()->trainer()->create();
        $user = User::factory()->for($trainer,'trainer')->create();
        $this->actingAs($trainer);

        $this->put("$this->resource/$user->id", ['email' => 'fake-test-mail@mail.com']);

        $this->response->assertForbidden();
    }
}
