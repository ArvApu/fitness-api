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
            'url' => $this->faker->boolean ? 'https://www.youtube.com/embed/dQw4w9WgXcQ' : null,
            'measurement' => $this->faker->randomElement(['seconds', 'minutes', 'grams', 'kilograms', 'quantity']),
        ];
    }
}
