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
            'photo' => UploadedFile::fake()->image('photo.png'),
            'degree_copy' => UploadedFile::fake()->image('degree.png'),
            'gender' => GenderType::MALE,
            'password' => 123456789,
        ];
    }
}
