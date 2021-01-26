<?php

namespace Database\Factories;

use App\Enums\GenderType;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class PatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Patient::class;

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
            'photo' => UploadedFile::fake()->image('photo1.png'),
            'verified_at' => null,
            'gender' => GenderType::MALE,
            'password' => '123456789',
        ];
    }
}
