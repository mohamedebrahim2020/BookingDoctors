<?php

namespace Tests\Feature;

use App\Models\Doctor;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DoctorRegisterationTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function doctor_successfully_registered()
    {
        Storage::fake('photo');
        Storage::fake('degree_copy');
        $data = Doctor::factory()->raw(); 
        $response = $this->postJson(route('doctorRegister'), $data, ["Accept"=>"application/json"]);
        $response->assertCreated();
    }

    /** @test */
    public function doctor_fail_to_register_with_photo_not_png()
    {
        Storage::fake('photo');
        Storage::fake('degree_copy');
        $data = Doctor::factory()->state(['photo' => UploadedFile::fake()->image('photo.jpg')])->raw();
        $response = $this->postJson(route('doctorRegister'), $data, ["Accept" => "application/json"]);
        $response->assertJsonValidationErrors('photo');
        $response->assertStatus(422);
    }
    /** @test */
    public function doctor_fail_to_register_with_no_gender()
    {
        Storage::fake('photo');
        Storage::fake('degree_copy');
        $data = Doctor::factory()->state(['gender' => '3'])->raw();
        $response = $this->postJson(route('doctorRegister'), $data, ["Accept" => "application/json"]);
        $response->assertJsonValidationErrors('gender');
        $response->assertStatus(422);
    }
}