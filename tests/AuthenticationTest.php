<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_login()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password'=> 'password',
        ]);

        $this->response->assertStatus(200);

        $this->response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    public function test_login_with_bad_credentials()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password'=> 'bad_password',
        ]);

        $this->response->assertStatus(401);
    }

    /**
     * @depends test_login
     */
    public function test_logout()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password'=> 'password',
        ]);

        $this->response->assertStatus(200);

        $content = json_decode($this->response->getContent());

        $this->post('/logout', [], ['Authorization' => "Bearer $content->access_token"]);

        $this->response->assertStatus(200);

        // After logout whe try one more time do the same thing, but this time we should be unauthorized.
        $this->post('/logout', [], ['Authorization' => "Bearer $content->access_token"]);
        $this->response->assertStatus(401);
    }

    /**
     * @depends test_login
     */
    public function test_refresh()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password'=> 'password',
        ]);

        $this->response->assertStatus(200);

        $content = json_decode($this->response->getContent());

        $this->post('/refresh', [], ['Authorization' => "Bearer $content->access_token"]);

        $this->response->assertStatus(200);

        $this->response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    public function test_me()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get('/me');

        $this->response->assertStatus(200);

        $content = json_decode($this->response->getContent());
        $this->assertEquals($user->id, $content->id);
    }
}
