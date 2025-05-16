<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Tenant;

class TenantControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Tenant $tenant1;
    private Tenant $tenant2;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations for the test database
        $this->artisan('migrate');

        // Create an admin user (who can perform all tenant actions)
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Create two tenant records (without user_id linkage)
        $this->tenant1 = Tenant::factory()->create();
        $this->tenant2 = Tenant::factory()->create();
    }

    /** @test */
    public function it_can_get_all_tenants()
    {
        // Fetch tenants as an authenticated admin user
        $response = $this->actingAs($this->admin)->getJson('/api/v1/tenants');

        // Check the response contains the two tenants we created
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')  // Assert two tenants in the response
            ->assertJsonFragment(['id' => $this->tenant1->id])  // Check tenant 1 ID
            ->assertJsonFragment(['id' => $this->tenant2->id]);  // Check tenant 2 ID
    }

    /** @test */
    public function it_can_create_a_tenant()
    {
        $tenantData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '123-456-7890',
            'address' => '123 Main St',
            'date_of_birth' => '1990-01-01',
            'emergency_contact_name' => 'Jane Doe',
            'emergency_contact_phone' => '987-654-3210'
        ];

        // Create a tenant as an authenticated admin
        $response = $this->actingAs($this->admin)->postJson('/api/v1/tenants', $tenantData);

        // Assert tenant creation success
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'John Doe'])
            ->assertJsonFragment(['message' => 'Tenant created successfully']);
    }

    /** @test */
    public function it_can_get_a_specific_tenant()
    {
        // Create a tenant
        $tenant = Tenant::factory()->create();

        // Get the tenant by ID
        $response = $this->actingAs($this->admin)->getJson("/api/v1/tenants/{$tenant->id}");

        // Assert we received the correct tenant data
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $tenant->id])
            ->assertJsonFragment(['name' => $tenant->name]);
    }

    /** @test */
    public function it_returns_404_if_tenant_not_found()
    {
        // Try fetching a tenant that doesn't exist
        $response = $this->actingAs($this->admin)->getJson('/api/v1/tenants/999');

        // Assert 404 error response
        $response->assertStatus(404)
            ->assertJson(['message' => 'Tenant not found']);
    }

    /** @test */
    public function it_can_update_a_tenant()
    {
        // Create a tenant
        $tenant = Tenant::factory()->create();

        // New data to update the tenant
        $updateData = [
            'name' => 'Updated Tenant Name',
            'email' => 'updated.email@example.com',
            'phone' => '987-654-3210',
            'address' => '456 Updated St',
            'date_of_birth' => '1992-02-02',
            'emergency_contact_name' => 'John Smith',
            'emergency_contact_phone' => '555-555-5555'
        ];

        // Update the tenant
        $response = $this->actingAs($this->admin)->putJson("/api/v1/tenants/{$tenant->id}", $updateData);

        // Assert successful update
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Tenant Name'])
            ->assertJsonFragment(['message' => 'Tenant updated successfully']);
    }

    /** @test */
    public function it_returns_404_if_tenant_to_update_not_found()
    {
        // Data to update
        $updateData = [
            'name' => 'Updated Tenant Name',
            'email' => 'updated.email@example.com',
            'phone' => '987-654-3210',
            'address' => '456 Updated St',
            'date_of_birth' => '1992-02-02',
            'emergency_contact_name' => 'John Smith',
            'emergency_contact_phone' => '555-555-5555'
        ];

        // Attempt to update a tenant that doesn't exist
        $response = $this->actingAs($this->admin)->putJson('/api/v1/tenants/999', $updateData);

        // Assert 404 error response
        $response->assertStatus(404)
            ->assertJson(['message' => 'Tenant not found']);
    }

    /** @test */
    public function it_can_delete_a_tenant()
    {
        // Create a tenant
        $tenant = Tenant::factory()->create();

        // Delete the tenant
        $response = $this->actingAs($this->admin)->deleteJson("/api/v1/tenants/{$tenant->id}");

        // Assert successful deletion
        $response->assertStatus(200)
            ->assertJson(['message' => 'Tenant deleted successfully']);
    }

    /** @test */
    public function it_returns_404_if_tenant_to_delete_not_found()
    {
        // Attempt to delete a tenant that doesn't exist
        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/tenants/999');

        // Assert 404 error response
        $response->assertStatus(404)
            ->assertJson(['message' => 'Tenant not found']);
    }

    /** @test */
    public function it_can_search_for_tenants()
    {
        // Create tenants with different names
        $tenant1 = Tenant::factory()->create(['name' => 'John Doe']);
        $tenant2 = Tenant::factory()->create(['name' => 'Jane Smith']);

        // Search for tenants with 'John' in their name
        $response = $this->actingAs($this->admin)->getJson('/api/v1/tenants?search=John');

        // Assert the search returns one tenant with 'John' in their name
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'John Doe']);
    }
}
