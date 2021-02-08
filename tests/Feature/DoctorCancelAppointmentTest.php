<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Notifications\AppointmentNotification;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DoctorCancelAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }
    /** @test */
    public function doctor_successfully_cancel_an_appointment()
    {
        $this->withoutExceptionHandling();
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
        Notification::fake();
        Queue::fake();
        $data = ['cancel_reason' => 'i have alot meetings'];
        $response = $this->postJson(route('appointments.cancel', ['appointment' =>$appointment->id]), $data, ["Accept" => "application/json"]);
        Notification::assertSentTo([$patient], AppointmentNotification::class);
        $response->assertOk();
    }

    /** @test */
    public function doctor_failed_to_cancel_not_found_appointment()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::APPROVED,
        ])->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        $data = ['cancel_reason' => 'i have alot meetings'];
        $response = $this->postJson(route('appointments.cancel', ['appointment' => 2]), $data, ["Accept" => "application/json"]);
        $response->assertNotFound();
    }

    /** @test */
    public function doctor_failed_to_cancel_an_appointment_not_belong_to_him()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $notBelongDoctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
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
        Passport::actingAs($notBelongDoctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        $data = ['cancel_reason' => 'i have alot meetings'];
        $response = $this->postJson(route('appointments.cancel', ['appointment' => $appointment->id]), $data, ["Accept" => "application/json"]);        $response->assertStatus(400);
    }

    /** @test */
    public function doctor_failed_to_cancel_an_appointment_that_already_canceled_or_not_approved()
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
            'status' => AppointmentStatus::CANCELLED,
        ])->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        $data = ['cancel_reason' => 'i have alot meetings'];
        $response = $this->postJson(route('appointments.cancel', ['appointment' => $appointment->id]), $data, ["Accept" => "application/json"]);        $response->assertStatus(400);
    }

    /** @test */
    public function doctor_failed_to_cancel_an_appointment_that_has_expired_date()
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
            'time' => Carbon::parse(now())->next('Monday')->subWeeks(2)->timestamp * 1000,
            'status' => AppointmentStatus::APPROVED,
        ])->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        $data = ['cancel_reason' => 'i have alot meetings'];
        $response = $this->postJson(route('appointments.cancel', ['appointment' => $appointment->id]), $data, ["Accept" => "application/json"]);        $response->assertStatus(400);
    }

    /** @test */
    public function doctor_failed_to_cancel_an_appointment_that_before_less_than_24_hrs()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => Carbon::parse(now())->addDay()->dayOfWeek,
                "is_all_day" => "1"
            ]
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time' => Carbon::parse(now())->addDay()->timestamp * 1000,
            'status' => AppointmentStatus::APPROVED,
        ])->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        $data = ['cancel_reason' => 'i have alot meetings'];
        $response = $this->postJson(route('appointments.cancel', ['appointment' => $appointment->id]), $data, ["Accept" => "application/json"]);
        $response->assertStatus(400);
    }
}