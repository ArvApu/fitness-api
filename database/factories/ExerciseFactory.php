<?php

namespace Database\Factories;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exercise::class;

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
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'measurement' => $this->faker->randomElement(['seconds', 'minutes', 'grams', 'kilograms', 'quantity']),
        ];
    }
}
