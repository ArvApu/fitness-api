<?php

namespace Database\Factories;

use App\Models\ExerciseLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExerciseLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'measurement_value' => $this->faker->randomNumber(2),
            'sets_count' => 10,
            'sets_done' => 10,
        ];
    }
}
