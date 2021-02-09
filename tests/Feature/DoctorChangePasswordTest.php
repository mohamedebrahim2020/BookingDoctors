<?php

namespace Tests\Feature;

use App\Models\Doctor;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DoctorChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }
    /** @test */
    public function doctor_successfully_change_his_password()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = [
            'old_password' => '123456789',
            'new_password' => '1234567',
            'new_password_confirmation' => '1234567',
        ];
        $response = $this->postJson(route('doctor.changePassword'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertOk();
    }

    /** @test */
    public function doctor_failed_to_change_password_with_wrong_old_password()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = [
            'old_password' => '12345678910', //wrong password
            'new_password' => '1234567',
            'new_password_confirmation' => '1234567',
        ];
        $response = $this->postJson(route('doctor.changePassword'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertUnauthorized();
    }

    /** @test */
    public function doctor_failed_to_change_password_with_wrong_unconfirmed_new_password()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = [
            'old_password' => '12345678910', //wrong password
            'new_password' => '1234567',
            'new_password_confirmation' => '12345',
        ];
        $response = $this->postJson(route('doctor.changePassword'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertStatus(422);
    }
}
