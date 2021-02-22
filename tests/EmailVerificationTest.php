<?php

use App\Models\User;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class EmailVerificationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_verify()
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();

        $token = encrypt(json_encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => Carbon::now()->addMinutes(10)->toDateTimeString(),
        ]));

        $this->post("/email/verify/$token");

        $this->response->assertStatus(200);

        $this->assertDatabaseMissing((new User)->getTable(), [
            'id' => $user->id,
            'email_verified_at' => null
        ]);
    }

    public function test_verify_with_bad_token()
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();

        $token = 'some_bad_token';

        $this->post("/email/verify/$token");

        $this->response->assertStatus(400);

        $this->assertDatabaseHas((new User)->getTable(), [
            'id' => $user->id,
            'email_verified_at' => null
        ]);
    }

    public function test_verify_with_null_data()
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();

        $token = encrypt(null);

        $this->post("/email/verify/$token");

        $this->response->assertStatus(400);

        $this->assertDatabaseHas((new User)->getTable(), [
            'id' => $user->id,
            'email_verified_at' => null
        ]);
    }

    public function test_verify_with_expired_token()
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();

        $token = encrypt(json_encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => Carbon::now()->subMinutes(10)->toDateTimeString(),
        ]));

        $this->post("/email/verify/$token");

        $this->response->assertStatus(400);

        $this->assertDatabaseHas((new User)->getTable(), [
            'id' => $user->id,
            'email_verified_at' => null
        ]);
    }

    public function test_resend()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post('/email/verification/resend');

        $this->response->assertStatus(200);
    }
}
