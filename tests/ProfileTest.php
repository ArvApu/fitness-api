<?php

use App\Models\User;
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
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'first_name' => 'Johnny',
            'last_name' => 'Test',
            'birthday' => '2006-01-01',
            'weight' => 80.5,
            'experience' => 10,
            'about' => 'Hello there!',
        ];

        $this->put($this->resource, $payload);

        $this->response->assertStatus(200);
        $this->response->assertJsonFragment($payload);

        $table = (new User)->getTable();

        $this->assertDatabaseHas($table, $payload);
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
