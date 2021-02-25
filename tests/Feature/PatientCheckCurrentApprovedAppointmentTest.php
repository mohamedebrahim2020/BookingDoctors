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

class PatientCheckCurrentApprovedAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    
    public function patient_successfully_check_current_appointment()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> strval(Carbon::parse(now())->dayOfWeek),
                "is_all_day"=> "1"
            ]
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::APPROVED,
            'time' => Carbon::parse(now())->timestamp ,
            'duration' => 30,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->postJson(route('patient.check.current.appointment', ["appointment" => $appointment->id]));
        $response->assertOk();

    }

    /** @test */

    public function patient_failed_to_check_current_appointment_as_it_is_not_approved()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => strval(Carbon::parse(now())->addDay(4)->dayOfWeek),
                "is_all_day" => "1"
            ]
        );
        $appointment =Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::PENDING,
            'time' => Carbon::parse(now())->addDays(4)->timestamp,
            'duration' => 30,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->postJson(route('patient.check.current.appointment', ["appointment" => $appointment->id]));
        $response->assertStatus(400);
    }
}