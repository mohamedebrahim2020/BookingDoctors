<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientVerificationCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class PatientVerificationCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PatientVerificationCode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => Str::random(10),
            'expired_at' => Carbon::now()->addHour(),
            'patient_id' => Patient::factory(),
        ];
    }
}
