<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class BroadcastTest extends TestCase
{
    use DatabaseMigrations;

    public function test_authenticate_via_get()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/broadcasting/auth?channel_name=private-user.'.$user->id);

        $this->response->assertStatus(200);
    }

    public function test_authenticate_via_post()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/broadcasting/auth', [
            'channel_name' => 'private-user.'.$user->id
        ]);

        $this->response->assertStatus(200);
    }

    public function test_fail_authenticate_when_no_user_is_authenticated()
    {
        $this->post('/broadcasting/auth', [
            'channel_name' => 'private-user.1'
        ]);

        $this->response->assertStatus(401);
    }
}
