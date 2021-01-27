<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorWorkingDays;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DoctorAddWorkingDayTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function doctor_successfully_add_working_day()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = DoctorWorkingDays::factory()->raw();
        $response = $this->postJson(route('workingdays.store'),$data);
        $response->assertCreated();        
    }

    /** @test */
    public function unactivated_doctor_is_forbidden_to_add_working_day()
    {
        $doctor = Doctor::factory()->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = DoctorWorkingDays::factory()->state(['doctor_id' => $doctor->id])->raw();
        $response = $this->postJson(route('workingdays.store'), $data);
        $response->assertForbidden();
    }

    /** @test */
    public function doctor_fail_to_add_working_day_with_invalid_day()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = [
            "working_days"=> [
                [
                    "day"=> "9",
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
        $response = $this->postJson(route('workingdays.store'), $data);
        $response->assertJsonValidationErrors('working_days.0.day');
        $response->assertStatus(422);
    }

        /** @test */
    public function doctor_fail_to_add_working_day_when_from_is_nullable_and_all_day_is_zero()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = [
            "working_days" => [
                [
                    "day" => "9",
                    "from" => "",
                    "to" => "",
                    "is_all_day" => "0"
                ],
                [
                    "day" => "3",
                    "is_all_day" => "1"
                ]
            ]
        ];
        $response = $this->postJson(route('workingdays.store'), $data);
        $response->assertJsonValidationErrors('working_days.0.from');
        $response->assertStatus(422);
    }

    /** @test */
    public function doctor_fail_to_add_working_day_when_from_is_nullable_and_all_day_is_nullable()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = [
            "working_days" => [
                [
                    "day" => "9",
                    "from" => "",
                    "to" => "",
                    "is_all_day" => ""
                ],
                [
                    "day" => "3",
                    "is_all_day" => "1"
                ]
            ]
        ];
        $response = $this->postJson(route('workingdays.store'), $data);
        $response->assertJsonValidationErrors('working_days.0.is_all_day');
        $response->assertStatus(422);
    }
}
