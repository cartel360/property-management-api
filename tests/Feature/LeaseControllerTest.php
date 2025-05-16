<?php

namespace Tests\Feature;

use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $landlord;
    protected $agent;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations for the test database
        $this->artisan('migrate');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->landlord = User::factory()->create(['role' => 'landlord']);
        $this->agent = User::factory()->create(['role' => 'agent']);
    }

    public function test_admin_can_create_lease(): void
    {
        // Create a Property record first
        $property = Property::factory()->create();

        // Now create the Unit record associated with the Property
        $unit = Unit::factory()->create(['property_id' => $property->id]);

        // Create a Tenant record
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/leases', [
                'unit_id' => $unit->id,
                'tenant_id' => $tenant->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addYear()->format('Y-m-d'),
                'monthly_rent' => 1000,
                'security_deposit' => 1500,
                'status' => 'active',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'start_date',
                    'end_date',
                    'monthly_rent',
                    'tenant' => ['id', 'name'],
                    'unit' => ['id', 'unit_number'],
                ],
                'message',
            ]);
    }

    public function test_landlord_can_view_their_property_leases(): void
    {
        // Create a Property for the landlord
        $property = Property::factory()->create(['landlord_id' => $this->landlord->id]);

        // Create a Unit associated with the Property
        $unit = Unit::factory()->create(['property_id' => $property->id]);

        // Create a Tenant
        $tenant = Tenant::factory()->create();

        // Create a Lease associated with the Unit and Tenant
        $lease = Lease::factory()->create([
            'unit_id' => $unit->id,
            'tenant_id' => $tenant->id
        ]);

        $response = $this->actingAs($this->landlord)
            ->getJson('/api/v1/leases');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $lease->id]);
    }

    public function test_agent_can_view_all_leases(): void
    {
        // Create a Property before creating Unit and Tenant
        $property = Property::factory()->create();

        // Create a Unit associated with the Property
        $unit = Unit::factory()->create(['property_id' => $property->id]);

        // Create a Tenant for the Lease
        $tenant = Tenant::factory()->create();

        // Create 3 leases associated with the created Unit and Tenant
        Lease::factory(3)->create([
            'unit_id' => $unit->id,
            'tenant_id' => $tenant->id,
        ]);

        $response = $this->actingAs($this->agent)
            ->getJson('/api/v1/leases');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_cannot_create_lease_for_occupied_unit(): void
    {
        // Create a property to associate with the unit
        $property = Property::factory()->create();

        // Create a unit associated with the property
        $unit = Unit::factory()->create(['property_id' => $property->id]);

        // Create a tenant
        $tenant = Tenant::factory()->create();

        // Create an active lease for the unit
        Lease::factory()->create([
            'unit_id' => $unit->id,
            'status' => 'active',
        ]);



        // Now, attempt to create another lease for the same unit
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/leases', [
                'unit_id' => $unit->id,
                'tenant_id' => $tenant->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addYear()->format('Y-m-d'),
                'monthly_rent' => 1000,
                'status' => 'active',
            ]);

        // Check for the custom message in the response
        $response->assertStatus(422)
            ->assertJson(['message' => 'Unit is already leased']);
    }


    public function test_can_update_lease(): void
    {
        // Create a Property before creating Unit and Tenant
        $property = Property::factory()->create();

        // Create a Unit associated with the Property
        $unit = Unit::factory()->create(['property_id' => $property->id]);

        // Create a Tenant for the Lease
        $tenant = Tenant::factory()->create();

        $lease = Lease::factory()->create();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/leases/{$lease->id}", [
                'monthly_rent' => 1200,
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['monthly_rent' => 1200]);
    }

    public function test_can_delete_lease(): void
    {
        // Create a Property before creating Unit and Tenant
        $property = Property::factory()->create();

        // Create a Unit associated with the Property
        $unit = Unit::factory()->create(['property_id' => $property->id]);

        // Create a Tenant for the Lease
        $tenant = Tenant::factory()->create();

        $lease = Lease::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/v1/leases/{$lease->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Lease deleted successfully']);

        $this->assertSoftDeleted($lease);
    }
}
