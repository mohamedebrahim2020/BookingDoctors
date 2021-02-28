<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GetDoctorReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }
    /** @test */
    public function patient_successfully_get_doctor_reviews()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        $appointments = Appointment::factory()->count(4)->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ])->create();
        foreach ($appointments as $appointment) {
            $appointment->review()->create([
                'rank' => rand(1,5),
                'comment' => 'goog'
            ]);
        }
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->getJson(route('get.doctor.reviews', ['doctor' =>$doctor->id]), ["Accept" => "application/json"]);
        $response->assertOk();
        $response->assertJsonCount(4, 'reviews');
    }

    /** @test */
    public function patient_successfully_get_empty_doctor_reviews()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        $appointments = Appointment::factory()->count(4)->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->getJson(route('get.doctor.reviews', ['doctor' => $doctor->id]), ["Accept" => "application/json"]);
        $response->assertOk();
        $response->assertJsonCount(0, 'reviews');
    }
}
