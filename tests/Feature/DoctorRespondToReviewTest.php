<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\PlatformType;
use App\Jobs\PushNotification;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DoctorRespondToReviewTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function doctor_successfully_respond_review()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Passport::actingAs($doctor, ['*'], 'doctor');
        $review = Review::factory()->for(Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ]))->create();
        $data = [
            'respond' => 'thanks'
        ];
        $patient->firebaseTokens()->updateOrCreate(
            ['platform' => PlatformType::WEB],
            ['token' => 'jjj'],
        );
        Queue::fake();
        Bus::fake();
        $response = $this->putJson(route('reviews.update', ['review' => $review->id]), $data, ["Accept" => "application/json"]);
        $response->assertOk();
        Bus::assertDispatched(PushNotification::class);
        Bus::assertDispatchedAfterResponse(PushNotification::class);
    }

    
    /** @test */
    public function doctor_failed_to_respond_not_found_review()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Passport::actingAs($doctor, ['*'], 'doctor');
        $review = Review::factory()->for(Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ]))->create();
        $data = [
            'respond' => 'thanks'
        ];
        $patient->firebaseTokens()->updateOrCreate(
            ['platform' => PlatformType::WEB],
            ['token' => 'jjj'],
        );
        Queue::fake();
        Bus::fake();
        $response = $this->putJson(route('reviews.update', ['review' => 2]), $data, ["Accept" => "application/json"]);
        $response->assertNotFound();
        Bus::assertNotDispatched(PushNotification::class);
        Bus::assertNotDispatchedAfterResponse(PushNotification::class);
    }
}
