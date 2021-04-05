<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_registration()
    {
        $this->post('/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
            'email' => 'john.doe@fake.mail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->response->assertStatus(201);

        $this->assertDatabaseHas((new User)->getTable(), [
            'role' => 'trainer',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@fake.mail.com',
        ]);
    }

    public function test_registration_of_invited_user()
    {
        $trainer = User::factory()->trainer()->create();

        $token = encrypt(json_encode([
            'trainer_id' => $trainer->id,
            'for' => 'doe.john.user@fake.mail.com',
        ]));

        $this->post('/register', [
            'first_name' => 'Doe',
            'last_name' => 'John',
            'gender' => 'male',
            'email' => 'doe.john.user@fake.mail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'token' => $token,
        ]);

        $this->response->assertStatus(201);

        $this->assertDatabaseHas((new User)->getTable(), [
            'role' => 'user',
            'first_name' => 'Doe',
            'last_name' => 'John',
            'gender' => 'male',
            'email' => 'doe.john.user@fake.mail.com',
        ]);
    }

    public function test_registration_of_user_with_not_intended_invite()
    {
        $trainer = User::factory()->trainer()->create();

        $token = encrypt(json_encode([
            'trainer_id' => $trainer->id,
            'for' => 'some.other.user@mail.org',
        ]));

        $this->post('/register', [
            'first_name' => 'Doe',
            'last_name' => 'John',
            'gender' => 'male',
            'email' => 'intended.doe.john.user@fake.mail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'token' => $token,
        ]);

        $this->response->assertStatus(400);

        $this->assertDatabaseMissing((new User)->getTable(), [
            'email' => 'intended.doe.john.user@fake.mail.com',
        ]);

        $this->assertDatabaseMissing((new User)->getTable(), [
            'email' => 'some.other.user@mail.org',
        ]);
    }

    public function test_registration_of_invited_user_with_bad_token()
    {
        $this->post('/register', [
            'first_name' => 'Doe',
            'last_name' => 'John',
            'gender' => 'male',
            'email' => 'doe.john.f@fake.mail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'token' => 'meh',
        ]);

        $this->response->assertStatus(400);
    }

    public function test_registration_of_invited_user_with_invalid_token_data()
    {
        $token = encrypt(json_encode([
            'for' => 'some.other.user@mail.org',
        ]));

        $this->post('/register', [
            'first_name' => 'Doe',
            'last_name' => 'John',
            'gender' => 'male',
            'email' => 'doe.john.f@fake.mail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'token' => $token,
        ]);

        $this->response->assertStatus(400);
    }
}
