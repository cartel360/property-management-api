<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Property;

class PropertyControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $landlord;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations for the test database
        $this->artisan('migrate');

        // Create admin and landlord users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->landlord = User::factory()->create(['role' => 'landlord']);
    }

    /** @test */
    public function it_can_get_all_properties()
    {
        $property1 = Property::factory()->create(['landlord_id' => $this->landlord->id]);
        $property2 = Property::factory()->create(['landlord_id' => $this->landlord->id]);

        $response = $this->actingAs($this->admin)->getJson('/api/v1/properties');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $property1->id])
            ->assertJsonFragment(['id' => $property2->id]);
    }

    /** @test */
    public function it_can_create_a_property()
    {
        $propertyData = [
            'name' => 'Test Property',
            'description' => 'A new test property',
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'features' => ['pool', 'garden'],
            'landlord_id' => $this->landlord->id
        ];

        $response = $this->actingAs($this->landlord)->postJson('/api/v1/properties', $propertyData);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Property'])
            ->assertJsonFragment(['message' => 'Property created successfully']);
    }

    /** @test */
    public function it_can_get_a_specific_property()
    {
        $property = Property::factory()->create(['landlord_id' => $this->landlord->id]);

        $response = $this->actingAs($this->admin)->getJson("/api/v1/properties/{$property->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $property->id])
            ->assertJsonFragment(['name' => $property->name]);
    }

    /** @test */
    public function it_returns_404_if_property_not_found()
    {
        $response = $this->actingAs($this->admin)->getJson('/api/v1/properties/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Property not found']);
    }

    /** @test */
    public function it_can_update_a_property()
    {
        $property = Property::factory()->create(['landlord_id' => $this->landlord->id]);

        $updateData = [
            'name' => 'Updated Property Name',
            'description' => 'Updated description',
            'address' => 'Updated address',
            'city' => 'Updated City',
            'state' => 'Updated State',
            'zip_code' => '54321',
            'features' => ['balcony', 'parking'],
        ];

        $response = $this->actingAs($this->landlord)->putJson("/api/v1/properties/{$property->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Property Name'])
            ->assertJsonFragment(['message' => 'Property updated successfully']);
    }

    /** @test */
    public function it_returns_404_if_property_to_update_not_found()
    {
        $updateData = [
            'name' => 'Updated Property Name',
            'description' => 'Updated description',
            'address' => 'Updated address',
            'city' => 'Updated City',
            'state' => 'Updated State',
            'zip_code' => '54321',
            'features' => ['balcony', 'parking'],
        ];

        $response = $this->actingAs($this->landlord)->putJson('/api/v1/properties/999', $updateData);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Property not found']);
    }

    /** @test */
    public function it_can_delete_a_property()
    {
        $property = Property::factory()->create(['landlord_id' => $this->landlord->id]);

        $response = $this->actingAs($this->landlord)->deleteJson("/api/v1/properties/{$property->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Property deleted successfully']);
    }

    /** @test */
    public function it_returns_404_if_property_to_delete_not_found()
    {
        $response = $this->actingAs($this->landlord)->deleteJson('/api/v1/properties/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Property not found']);
    }
}
