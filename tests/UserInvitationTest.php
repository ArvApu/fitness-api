<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserInvitationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_invite()
    {
        $this->actingAs(User::factory()->trainer()->create());

        $this->post('/users/invite', [
            'email' => 'john.doe@fake.mail.com',
        ]);

        $this->response->assertStatus(200);
    }

    public function test_confirm()
    {
        $trainer = User::factory()->trainer()->create();
        $client = User::factory()->create();

        $token = encrypt(json_encode([
            'trainer_id' => $trainer->id,
            'for' => $client->email,
        ]));

        $this->post("/users/invite/$token");

        $this->response->assertStatus(200);

        $this->assertDatabaseHas((new User)->getTable(), [
            'trainer_id' => $trainer->id,
            'email' => $client->email,
        ]);
    }

    public function test_confirm_with_bad_token()
    {
        $this->post('/users/invite/meh');
        $this->response->assertStatus(400);
    }

    public function test_confirm_with_invalid_token_data()
    {
        $token = encrypt(json_encode([
            'for' => 'some.other.user@mail.org',
        ]));

        $this->post("/users/invite/$token");

        $this->response->assertStatus(400);
    }
}
