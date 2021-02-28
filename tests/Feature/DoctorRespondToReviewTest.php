<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Jobs\PushNotification;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
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
    public function Admin_successfully_delete_review()
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
        Passport::actingAs($doctor, ['*'], 'doctor');
        $review = Review::factory()->for(Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ]))->create();
        $data = [
            'respond' => 'thanks'
        ];
        Bus::fake();
        $response = $this->putJson(route('reviews.update', ['review' => $review->id]), $data, ["Accept" => "application/json"]);
        $response->assertOk();
        Bus::assertDispatched(PushNotification::class);
        Bus::assertDispatchedAfterResponse(PushNotification::class);
    }
}
