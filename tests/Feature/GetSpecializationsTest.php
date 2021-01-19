<?php

namespace Tests\Feature;

use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetSpecializationsTest extends TestCase
{
    use RefreshDatabase;

    protected $routeName = '/api/specializations';

    /** @test */
    public function successfully_get_specializations()
    {
        $this->seed(DoctorSpecializationsSeeder::class);
        $response = $this->getJson($this->routeName);
        $response->assertExactJson([
            [
                "id" => 1,
                "name" => "ortho"
            ],
            [
                "id" => 2,
                "name" => "dentist"
            ],
            [
                "id" => 3,
                "name" => "knee"
            ]
        ]);
        $response->assertOk();
    }
    /** @test */
    public function get_empty_specializations()
    {
        $response = $this->getJson($this->routeName);
        $response->assertExactJson([]);
        $response->assertOk();
    }
}
