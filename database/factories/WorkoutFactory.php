<?php

namespace Database\Factories;

use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkoutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Workout::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->words(10, true),
            'type' => $this->faker->randomElement(['weight', 'cardio', 'hiit', 'recovery', 'general']),
            'is_private' => false,
        ];
    }

    /**
     * @return WorkoutFactory
     */
    public function private(): WorkoutFactory
    {
        return $this->state([
            'is_private' => true,
        ]);
    }
}
