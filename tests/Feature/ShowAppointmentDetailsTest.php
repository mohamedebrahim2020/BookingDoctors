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

class ShowAppointmentDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }
    /** @test */
    public function doctor_successfully_get_an_appointment_details_belongs_to_him()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::APPROVED,
        ])->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        $response = $this->getJson(route('appointments.doctor.show', ['appointment' =>$appointment->id]), ["Accept" => "application/json"]);
        $response->assertOk();
    }

    /** @test */
    public function patient_successfully_get_an_appointment_details_belongs_to_him()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::APPROVED,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->getJson(route('appointments.patient.show', ['appointment' => $appointment->id]), ["Accept" => "application/json"]);
        $response->assertOk();
    }

    /** @test */
    public function doctor_fail_to_get_an_appointment_details_not_belongs_to_him()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::APPROVED,
        ])->create();
        $unauthorizedDoctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        Passport::actingAs($unauthorizedDoctor, ['*'], 'doctor');
        $response = $this->getJson(route('appointments.doctor.show', ['appointment' => $appointment->id]), ["Accept" => "application/json"]);
        $response->assertForbidden();
    }

    /** @test */
    public function patient_fail_to_get_an_appointment_details_not_belongs_to_him()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::APPROVED,
        ])->create();
        $unauthorizedPatient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        Passport::actingAs($unauthorizedPatient, ['*'], 'patient');
        $response = $this->getJson(route('appointments.patient.show', ['appointment' => $appointment->id]), ["Accept" => "application/json"]);
        $response->assertForbidden();
    }
}
