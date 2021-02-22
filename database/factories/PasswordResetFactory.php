<?php

namespace Database\Factories;

use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasswordResetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PasswordReset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'token' => hash('sha256', 'some_testing_token'),
            'expires_at' => Carbon::now()->addMinutes(10),
        ];
    }

    /**
     * Define the model's default state.
     *
     * @return PasswordResetFactory
     */
    public function expired(): PasswordResetFactory
    {
        return $this->state([
            'expires_at' => Carbon::now()->subHour(),
        ]);
    }
}
