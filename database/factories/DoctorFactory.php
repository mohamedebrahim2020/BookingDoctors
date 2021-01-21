<?php

namespace Database\Factories;

use App\Enums\GenderType;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class DoctorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Doctor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->phoneNumber,
            'specialization_id' => $this->faker->numberBetween($min = 1, $max = 3),
            'photo' => $this->faker->imageUrl(),
            'degree_copy' => $this->faker->imageUrl(),
            'activated_at' => null,
            'gender' => GenderType::MALE,
            'password' => 123456789,
        ];
    }
}
