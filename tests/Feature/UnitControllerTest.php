<?php

namespace Tests\Feature;

use App\Models\{User, Property, Unit};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Property $property;
    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations for the test database
        $this->artisan('migrate');

        // Create an admin user
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Create a landlord record and a Property
        $landlord = User::factory()->create(['role' => 'landlord']);
        $this->property = Property::factory()->create(['landlord_id' => $landlord->id]);

        // Create a unit associated with the property
        $this->unit = Unit::factory()->create(['property_id' => $this->property->id]);
    }

    /** @test */
    public function it_can_get_all_units_for_property()
    {
        // Create more units for the property
        $unit2 = Unit::factory()->create(['property_id' => $this->property->id]);
        $unit3 = Unit::factory()->create(['property_id' => $this->property->id]);

        $response = $this->actingAs($this->admin)->getJson("/api/v1/properties/{$this->property->id}/units");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')  // Assert there are 3 units for this property
            ->assertJsonFragment(['id' => $this->unit->id])
            ->assertJsonFragment(['id' => $unit2->id])
            ->assertJsonFragment(['id' => $unit3->id]);
    }

    /** @test */
    public function it_can_create_a_unit_for_property()
    {
        $unitData = [
            'unit_number' => 'B102',
            'rent_amount' => 1500.00,
            'size' => 70,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'features' => ['balcony', 'parking'],
            'status' => 'vacant',
        ];

        $response = $this->actingAs($this->admin)->postJson("/api/v1/properties/{$this->property->id}/units", $unitData);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Unit created successfully'])
            ->assertJsonFragment(['unit_number' => 'B102'])
            ->assertJsonFragment(['status' => 'vacant']);
    }

    /** @test */
    public function it_can_get_a_specific_unit()
    {
        $response = $this->actingAs($this->admin)->getJson("/api/v1/units/{$this->unit->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $this->unit->id])
            ->assertJsonFragment(['unit_number' => $this->unit->unit_number]);
    }

    /** @test */
    public function it_returns_404_if_unit_not_found()
    {
        $response = $this->actingAs($this->admin)->getJson('/api/v1/units/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Unit not found']);
    }

    /** @test */
    public function it_can_update_a_unit()
    {
        $updateData = [
            'unit_number' => 'A103',
            'rent_amount' => 1800.00,
            'size' => 80,
            'bedrooms' => 4,
            'bathrooms' => 2,
            'features' => ['balcony', 'garage'],
            'status' => 'occupied',
        ];

        $response = $this->actingAs($this->admin)->putJson("/api/v1/units/{$this->unit->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['unit_number' => 'A103'])
            ->assertJsonFragment(['status' => 'occupied']);
    }

    /** @test */
    public function it_returns_404_if_unit_to_update_not_found()
    {
        $updateData = [
            'unit_number' => 'A104',
            'rent_amount' => 2000.00,
            'size' => 90,
            'bedrooms' => 5,
            'bathrooms' => 3,
            'features' => ['balcony', 'pool'],
            'status' => 'vacant',
        ];

        $response = $this->actingAs($this->admin)->putJson('/api/v1/units/999', $updateData);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Unit not found']);
    }

    /** @test */
    public function it_can_delete_a_unit()
    {
        $response = $this->actingAs($this->admin)->deleteJson("/api/v1/units/{$this->unit->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Unit deleted successfully']);
    }

    /** @test */
    public function it_returns_404_if_unit_to_delete_not_found()
    {
        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/units/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Unit not found']);
    }

}
