<?php

namespace Tests\Feature;

use App\Models\Admin;
use Database\Seeders\AdminPermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GetPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(SuperAdminSeeder::class);
        $this->seed(AdminPermissionSeeder::class);
        $this->artisan('passport:client', ['--password' => null, '--no-interaction' => true, '--provider' => 'admins', '--name' => 'AdminGrantClient']);
    }

    /** @test */
    public function superadmin_successfully_get_permissions()
    {
        $admin = Admin::where('is_super', 1)->first();
        Passport::actingAs($admin, ['*'], 'admin');
        $response = $this->getJson('/api/admin/permissions');
        $response->assertOk();
    }
    /** @test */
    public function unauthenticated_superadmin_fail_to_get_permissions()
    {
        $response = $this->getJson('/api/admin/permissions');
        $response->assertStatus(401);
    }
}
