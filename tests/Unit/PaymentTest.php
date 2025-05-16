<?php

namespace Tests\Unit;

use App\Jobs\SendPaymentReceipt;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Models\Lease;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the database tables are migrated before each test
        Artisan::call('migrate');

        // Create a landlord record explicitly
        $landlord = User::factory()->create(['role' => 'landlord']);
    }


    /** @test */
    public function it_dispatches_payment_receipt_job()
    {
        // Create a Property record first
        $property = Property::factory()->create();

        // Now create the Unit record associated with the Property
        $unit = Unit::factory()->create(['property_id' => $property->id]);

        // Create a Tenant record
        $tenant = Tenant::factory()->create();

        // Create a Lease associated with the Tenant and Unit
        $lease = Lease::factory()->create([
            'tenant_id' => $tenant->id,
            'unit_id' => $unit->id,
        ]);

        // Mock the Log facade
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Payment receipt sent');
            });

        $payment = Payment::factory()->create();

        // Dispatch the job
        dispatch(new SendPaymentReceipt($payment));
    }
}
