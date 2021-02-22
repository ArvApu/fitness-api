<?php

use App\Models\PasswordReset;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class PasswordResetTest extends TestCase
{
    use DatabaseMigrations;

    public function test_send_password_reset()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->assertDatabaseMissing((new PasswordReset)->getTable(), [
            'email' => $user->email,
        ]);

        $this->post('/password/reset', [
            'email' => $user->email,
        ]);

        $this->response->assertStatus(200);

        $this->assertDatabaseHas((new PasswordReset)->getTable(), [
            'email' => $user->email,
        ]);
    }

    public function test_reset_password()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var PasswordReset $passwordReset */
        $passwordReset = PasswordReset::factory()->create(['email' => $user->email]);

        $this->post('/password/reset/some_testing_token', [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->response->assertStatus(200);

        $this->assertDatabaseMissing((new PasswordReset)->getTable(), ['id' => $passwordReset->id]);
    }

    public function test_reset_password_with_expired_reset()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var PasswordReset $passwordReset */
        PasswordReset::factory()->expired()->create(['email' => $user->email]);

        $this->post('/password/reset/some_testing_token', [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->response->assertStatus(400);
    }
}
