<?php

namespace Database\Factories;

use App\Models\Day;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Day::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->words(2, true),
            'information' => $this->faker->words(5, true),
            'date' => Carbon::now(),
        ];
    }
}
