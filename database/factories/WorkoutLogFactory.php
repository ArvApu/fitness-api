<?php

namespace Database\Factories;

use App\Models\WorkoutLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkoutLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkoutLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => $this->faker->randomElement(['missed', 'interrupted', 'completed']),
            'comment' => $this->faker->words(10, true),
            'difficulty' => $this->faker->randomElement(['easy', 'moderate', 'hard', 'exhausting']),
        ];
    }

    /**
     * @return WorkoutLogFactory
     */
    public function missed(): WorkoutLogFactory
    {
        return $this->state([
            'status' => 'missed',
        ]);
    }

    /**
     * @return WorkoutLogFactory
     */
    public function interrupted(): WorkoutLogFactory
    {
        return $this->state([
            'status' => 'interrupted',
        ]);
    }

    /**
     * @return WorkoutLogFactory
     */
    public function completed(): WorkoutLogFactory
    {
        return $this->state([
            'status' => 'completed',
        ]);
    }
}
