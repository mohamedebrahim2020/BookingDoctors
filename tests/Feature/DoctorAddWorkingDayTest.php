<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorWorkingDays;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DoctorAddWorkingDayTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function doctor_successfully_add_working_day()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = DoctorWorkingDays::factory()->state(['doctor_id' => $doctor->id])->raw();
        $response = $this->postJson(route('workingdays.store'),$data);
        $response->assertCreated();        
    }

    /** @test */
    public function unactivated_doctor_is_forbidden_to_add_working_day()
    {
        $doctor = Doctor::factory()->create();
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = DoctorWorkingDays::factory()->state(['doctor_id' => $doctor->id])->raw();
        $response = $this->postJson(route('workingdays.store'), $data);
        $response->assertForbidden();
    }

    /** @test */
    public function doctor_fail_to_add_working_day_with_invalid_day()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = DoctorWorkingDays::factory()->state(['day' => 9,'doctor_id' => $doctor->id])->raw();
        $response = $this->postJson(route('workingdays.store'), $data);
        $response->assertJsonValidationErrors('day');
        $response->assertStatus(422);
    }
}
