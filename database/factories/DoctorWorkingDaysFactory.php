<?php

namespace Database\Factories;

use App\Enums\WeekDays;
use App\Models\Doctor;
use App\Models\DoctorWorkingDays;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorWorkingDaysFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DoctorWorkingDays::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "working_days" => [
                [
                    "day"=> "1",
                    "from"=> "10:00 PM",
                    "to"=> "11:00 PM",
                    "is_all_day"=> "0"
                ],
                [
                    "day"=> "3",
                    "is_all_day"=> "1"
                ]
            ]
        ];
    }
}
