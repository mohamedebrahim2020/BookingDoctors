<?php

namespace Tests\Feature;

use App\Models\Specialization;
use Closure;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GetSpecializationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function successfully_get_specializations()
    {
        $this->seed(DoctorSpecializationsSeeder::class);
        $specializations = Specialization::all();
        Cache::shouldReceive('remember')
        ->once()
        ->with('specializations', 33600, Closure::class)
        ->andReturn($specializations);
        $response = $this->getJson(route('specializations.index'));
        $response->assertOk();
        $response->assertJsonCount($specializations->count());
    }
    /** @test */
    public function get_empty_specializations()
    {
        $response = $this->getJson(route('specializations.index'));
        $response->assertExactJson([]);
        $response->assertOk();
    }
}
