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

class DeleteAdminTest extends TestCase
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
    public function superadmin_successfully_delete_admin()
    {
        $superAdmin = Admin::where('is_super', 1)->first();
        Passport::actingAs($superAdmin, ['*'], 'admin');
        $admin = Admin::factory()->create();
        $response = $this->deleteJson('/api/admins/' . $admin->id);
        $response->assertOk();
    }

    /** @test */
    public function admin_fail_to_delete_admin()
    {
        Passport::actingAs(Admin::factory()->create(), ['*'], 'admin');
        $admin = Admin::factory()->create();
        $response = $this->deleteJson('/api/admins/' . $admin->id);
        $response->assertExactJson(["only super admin can delete admin and not be deleted"]);
        $response->assertForbidden();
    }

    /** @test */
    public function superadmin_fail_to_delete_superadmin()
    {
        $superAdmin = Admin::where('is_super', 1)->first();
        Passport::actingAs($superAdmin, ['*'], 'admin');
        $anotherSuperAdmin = Admin::factory()->create(['is_super' => 1]);
        $response = $this->deleteJson('/api/admins/' . $anotherSuperAdmin->id);
        $response->assertExactJson(["only super admin can delete admin and not be deleted"]);
        $response->assertForbidden();
    }
}
