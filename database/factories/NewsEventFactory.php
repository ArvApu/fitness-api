<?php

namespace Database\Factories;

use App\Models\NewsEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsEventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NewsEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content' => $this->faker->words(2, true),
        ];
    }
}
