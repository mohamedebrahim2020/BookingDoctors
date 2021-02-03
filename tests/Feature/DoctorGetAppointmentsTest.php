<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DoctorGetAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }
    /** @test */
    public function doctor_successfully_get_Pending_appointments()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Appointment::factory()->count(30)->state(new Sequence(
            ['status' => AppointmentStatus::PENDING],
            ['status' => AppointmentStatus::APPROVED],
        ))->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
        ]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $response = $this->getJson(route('appointments.index', ['status' => AppointmentStatus::PENDING]), ["Accept" => "application/json"]);
        $response->assertOk();
        $response->assertJsonCount(15, $key = null);
        $this->assertDatabaseCount('appointments', 30);
    }

    /** @test */
    public function doctor_successfully_get_approved_appointments()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        Appointment::factory()->count(30)->state(new Sequence(
            ['status' => AppointmentStatus::PENDING],
            ['status' => AppointmentStatus::APPROVED],
        ))->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
        ]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $response = $this->getJson(route('appointments.index', ['status' => AppointmentStatus::APPROVED]), ["Accept" => "application/json"]);
        $response->assertOk();
        $response->assertJsonCount(15, $key = null);
        $this->assertDatabaseCount('appointments', 30);
    }

    /** @test */
    public function doctor_successfully_get_all_appointments_without_query_params()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        Appointment::factory()->count(30)->state(new Sequence(
            ['status' => AppointmentStatus::PENDING],
            ['status' => AppointmentStatus::APPROVED],
        ))->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
        ]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $response = $this->getJson(route('appointments.index'), ["Accept" => "application/json"]);
        $response->assertOk();
        $response->assertJsonCount(30, $key = null);
        $this->assertDatabaseCount('appointments', 30);
    }

}
