<?php

namespace Tests\Unit;

use App\Jobs\SendRentReminder;
use App\Models\Lease;
use App\Models\User;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RentReminderTest extends TestCase
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
    public function it_logs_rent_reminder()
    {
        // Create a Property record first
        $property = Property::factory()->create();

        // Now create the Unit record associated with the Property
        $unit = Unit::factory()->create(['property_id' => $property->id]);

        // Create a Tenant record
        $tenant = Tenant::factory()->create();


        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'Rent reminder sent');
            });

        $lease = Lease::factory()->create();

        SendRentReminder::dispatch($lease);
    }
}
