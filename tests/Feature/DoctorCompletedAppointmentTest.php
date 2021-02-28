<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\PlatformType;
use App\Jobs\PushNotification;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Notifications\AppointmentNotification;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DoctorCompletedAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }
    /** @test */
    public function doctor_successfully_complete_an_appointment()
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
            'time' => Carbon::parse(now())->subHours(2)->timestamp,
            'status' => AppointmentStatus::CHECKED,
        ])->create();
        $patient->firebaseTokens()->updateOrCreate(
            ['platform' => PlatformType::WEB],
            ['token' => 'jjj'],
        );
        Passport::actingAs($doctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        Bus::fake();
        $response = $this->postJson(route('appointments.complete', ['appointment' =>$appointment->id]), ["Accept" => "application/json"]);
        Notification::assertSentTo([$patient], AppointmentNotification::class);
        $response->assertOk();
        Bus::assertDispatched(PushNotification::class);
        Bus::assertDispatchedAfterResponse(PushNotification::class);
    }

    /** @test */
    public function doctor_failed_to_complete_not_found_appointment()
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
            'status' => AppointmentStatus::CHECKED,
        ])->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        $response = $this->postJson(route('appointments.complete', ['appointment' => 2]), ["Accept" => "application/json"]);
        $response->assertNotFound();
    }

    /** @test */
    public function doctor_failed_to_complete_an_appointment_not_belong_to_him()
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
            'status' => AppointmentStatus::CHECKED,
        ])->create();
        Passport::actingAs($notBelongDoctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        $response = $this->postJson(route('appointments.complete', ['appointment' => $appointment->id]), ["Accept" => "application/json"]);
        $response->assertForbidden();
    }

    /** @test */
    public function doctor_failed_to_complete_an_appointment_that_already_completed()
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
            'status' => AppointmentStatus::COMPLETED,
        ])->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        $response = $this->postJson(route('appointments.complete', ['appointment' => $appointment->id]), ["Accept" => "application/json"]);
        $response->assertStatus(400);
    }

    /** @test */
    public function doctor_failed_to_complete_an_appointment_that_finished_after_now()
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
            'time' => Carbon::parse(now())->addHour()->timestamp,
            'status' => AppointmentStatus::PENDING,
        ])->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        Notification::fake();
        Queue::fake();
        $response = $this->postJson(route('appointments.complete', ['appointment' => $appointment->id]), ["Accept" => "application/json"]);
        $response->assertStatus(400);
    }
}
