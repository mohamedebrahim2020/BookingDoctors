<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'appointment_id' => Appointment::factory(),
            'rank' => rand(1,5),
            'comment' => $this->faker->text(20) ,
            'respond' => $this->faker->text(20),
        ];
    }
}
