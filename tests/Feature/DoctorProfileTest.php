<?php

namespace Tests\Feature;

use App\Models\Doctor;
use Carbon\Carbon;
use Closure;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DoctorProfileTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }
    /** @test */
    public function doctor_successfully_get_his_profile()
    {
        $doctor = Doctor::factory()->create([
            "activated_at" => Carbon::now(),            
            'photo' => 'photo1.png',
            'degree_copy' => 'photo2.png'
        ]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Passport::actingAs($doctor, ['*'], 'doctor');
        $keyName = 'doctor_' . $doctor->id;
        Cache::shouldReceive('remember')
        ->once()
        ->with($keyName, 33600, Closure::class)
        ->andReturn($doctor);
        $response = $this->getJson(route('doctor.profile'));
        $response->assertOk();
        $response->assertJsonStructure([
            "id",
            "name",
            "email",
            "specialization",
            "photo",
            "degree_copy",
            "is_active",
            "regestired_at",
            "working_days"
        ]);
    }
    
    /** @test */
    public function unactivated_doctor_failed_to_get_his_profile()
    {
        $doctor = Doctor::factory()->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        $response = $this->getJson(route('doctor.profile'));
        $response->assertForbidden();
    }
}
