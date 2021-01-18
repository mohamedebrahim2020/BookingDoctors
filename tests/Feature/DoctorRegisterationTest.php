<?php

namespace Tests\Feature;

use App\Models\Doctor;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpParser\Comment\Doc;
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
        $data = [
            'name' => 'hima',
            'email' => 'hima@gmail.com',
            'phone' => '01225000539',
            'specialization_id' => 1,
            'photo' => UploadedFile::fake()->image('photo.png'),
            'degree_copy' => UploadedFile::fake()->image('degree.png'),
            'gender' => '1',
            'password' => '123456789',
        ]; 
        $response = $this->postJson('/api/doctor/register', $data, ["Accept"=>"application/json"]);
        $response->assertCreated();
        Storage::disk('photo')->exists($data['photo']->hashName());
        Storage::disk('degree_copy')->exists($data['degree_copy']->hashName());
    }

    /** @test */
    public function doctor_fail_to_register_with_photo_not_png()
    {
        Storage::fake('photo');
        Storage::fake('degree_copy');
        $data = [
            'name' => 'hima',
            'email' => 'hima@gmail.com',
            'phone' => '01225000539',
            'specialization_id' => 1,
            'photo' => UploadedFile::fake()->image('photo.jpg'),
            'degree_copy' => UploadedFile::fake()->image('degree.png'),
            'gender' => '1',
            'password' => '123456789',
        ];
        $response = $this->postJson('/api/doctor/register', $data, ["Accept" => "application/json"]);
        $response->assertJsonValidationErrors('photo');
        $response->assertStatus(422);
    }
    /** @test */
    public function doctor_fail_to_register_with_no_gender()
    {
        Storage::fake('photo');
        Storage::fake('degree_copy');
        $data = [
            'name' => 'hima',
            'email' => 'hima@gmail.com',
            'phone' => '01225000539',
            'specialization_id' => 1,
            'photo' => UploadedFile::fake()->image('photo.png'),
            'degree_copy' => UploadedFile::fake()->image('degree.png'),
            'gender' => '3',
            'password' => '123456789',
        ];
        $response = $this->postJson('/api/doctor/register', $data, ["Accept" => "application/json"]);
        $response->assertJsonValidationErrors('gender');
        $response->assertStatus(422);
    }
}