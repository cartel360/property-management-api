<?php

namespace Tests\Feature;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Tenant $tenant;
    private Lease $lease;
    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations for the test database
        $this->artisan('migrate');

        $this->admin = User::factory()->create(['role' => 'admin']);

        // Create a landlord record explicitly
        $landlord = User::factory()->create(['role' => 'landlord']);

        // Create a Property record with the landlord
        $property = Property::factory()->create(['landlord_id' => $landlord->id]);

        // Create a tenant and a unit
        $this->tenant = Tenant::factory()->create();
        $this->unit = Unit::factory()->create();

        // Create a lease associated with the tenant and unit
        $this->lease = Lease::factory()->create([
            'tenant_id' => $this->tenant->id,
            'unit_id' => $this->unit->id,
        ]);
    }

    /** @test */
    public function it_can_get_all_payments()
    {
        // Create some payments
        $payment1 = Payment::factory()->create(['lease_id' => $this->lease->id]);
        $payment2 = Payment::factory()->create(['lease_id' => $this->lease->id]);

        $response = $this->actingAs($this->admin)->getJson('/api/v1/payments');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $payment1->id])
            ->assertJsonFragment(['id' => $payment2->id]);
    }

    /** @test */
    public function it_can_create_a_payment()
    {
        $paymentData = [
            'lease_id' => $this->lease->id,
            'amount' => 200.00,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'Credit Card',
            'status' => 'completed',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/v1/payments', $paymentData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Payment recorded successfully',
            ])
            ->assertJsonFragment([
                'amount' => 200.00,
                'payment_method' => 'Credit Card',
            ]);
    }

    /** @test */
    public function it_can_get_a_specific_payment()
    {
        $payment = Payment::factory()->create(['lease_id' => $this->lease->id]);

        $response = $this->actingAs($this->admin)->getJson("/api/v1/payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $payment->id]);
    }

    /** @test */
    public function it_returns_404_if_payment_not_found()
    {
        $response = $this->actingAs($this->admin)->getJson('/api/v1/payments/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Payment not found']);
    }

    /** @test */
    public function it_can_update_a_payment()
    {
        $payment = Payment::factory()->create(['lease_id' => $this->lease->id]);

        $updateData = [
            'amount' => 300.00,
            'payment_method' => 'Debit Card',
            'status' => 'completed',
            'notes' => 'Updated payment details',
        ];

        $response = $this->actingAs($this->admin)->putJson("/api/v1/payments/{$payment->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['amount' => 300.00])
            ->assertJsonFragment(['payment_method' => 'Debit Card']);
    }

    /** @test */
    public function it_returns_404_if_payment_to_update_not_found()
    {
        $updateData = [
            'amount' => 300.00,
            'payment_method' => 'Debit Card',
            'status' => 'completed',
            'notes' => 'Updated payment details',
        ];

        $response = $this->actingAs($this->admin)->putJson('/api/v1/payments/999', $updateData);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Payment not found']);
    }

    /** @test */
    public function it_can_delete_a_payment()
    {
        $payment = Payment::factory()->create(['lease_id' => $this->lease->id]);

        $response = $this->actingAs($this->admin)->deleteJson("/api/v1/payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Payment deleted successfully']);
    }

    /** @test */
    public function it_returns_404_if_payment_to_delete_not_found()
    {
        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/payments/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Payment not found']);
    }
}
