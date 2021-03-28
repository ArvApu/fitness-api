<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'role' => 'user',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password,
            'birthday' => $this->faker->date(),
            'last_login_at' => Carbon::now(),
            'email_verified_at' => Carbon::now(),
        ];
    }

    /**
     * @return UserFactory
     */
    public function unverified(): UserFactory
    {
        return $this->state([
            'email_verified_at' => null,
        ]);
    }

    /**
     * @return UserFactory
     */
    public function trainer(): UserFactory
    {
        return $this->state([
            'role' => 'trainer',
        ]);
    }

    /**
     * @return UserFactory
     */
    public function admin(): UserFactory
    {
        return $this->state([
            'role' => 'admin',
        ]);
    }
}
