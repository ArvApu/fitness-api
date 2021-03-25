<?php

use App\Models\NewsEvent;
use App\Models\User;
use App\Models\UserLog;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = 'profile';

    public function test_update()
    {
        $trainer = User::factory()->trainer()->create();
        $user = User::factory()->for($trainer, 'trainer')->create();
        $this->actingAs($user);

        $payload = [
            'first_name' => 'Johnny',
            'last_name' => 'Test',
            'birthday' => '2006-01-01',
            'weight' => $user->weight + 10,
            'experience' => 10,
            'about' => 'Hello there!',
        ];

        $this->put($this->resource, $payload);

        $this->response->assertStatus(200);
        $this->response->assertJsonFragment($payload);

        $table = (new User)->getTable();

        $this->assertDatabaseHas($table, $payload);

        // Should also log
        $this->assertDatabaseHas((new NewsEvent)->getTable(), ['user_id' => $trainer->id]);
        $this->assertDatabaseHas((new UserLog())->getTable(), ['user_id' => $user->id]);
    }

    public function test_change_password()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'password' => 'password',
            'new_password' => 'Pa$$w0rd',
            'new_password_confirmation' => 'Pa$$w0rd',
        ];

        $this->put("$this->resource/password", $payload);

        $this->response->assertNoContent();
    }

    public function test_fail_to_change_password_without_providing_old_one()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'new_password' => 'Pa$$w0rd',
            'new_password_confirmation' => 'Pa$$w0rd',
        ];

        $this->put("$this->resource/password", $payload);

        $this->response->assertForbidden();
    }

    public function test_fail_to_change_password_with_bad_old_one()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'password' => '',
            'new_password' => 'Pa$$w0rd',
            'new_password_confirmation' => 'Pa$$w0rd',
        ];

        $this->put("$this->resource/password", $payload);

        $this->response->assertForbidden();
    }
}
