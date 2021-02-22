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
            'email' => 'john.doe@fake.mail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->response->assertStatus(201);

        $this->assertDatabaseHas((new User)->getTable(), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@fake.mail.com',
        ]);
    }
}
