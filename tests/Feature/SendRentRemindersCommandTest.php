<?php

namespace Tests\Feature;

use App\Jobs\SendRentReminder;
use App\Models\Lease;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SendRentRemindersCommandTest extends TestCase
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
    public function it_dispatches_jobs_for_active_leases()
    {
        // Create a Property record first
        $property = Property::factory()->create();

        // Now create the Unit record associated with the Property
        $unit = Unit::factory()->create(['property_id' => $property->id]);

        // Create a Tenant record
        $tenant = Tenant::factory()->create();

        Bus::fake();

        $activeLease = Lease::factory()->create(['status' => 'active']);
        $inactiveLease = Lease::factory()->create(['status' => 'ended']);

        $this->artisan('reminders:rent')
            ->expectsOutput("Sent rent reminders for 1 leases")
            ->assertExitCode(0);

        Bus::assertDispatched(SendRentReminder::class, function ($job) use ($activeLease) {
            return $job->lease->id === $activeLease->id;
        });

        Bus::assertNotDispatched(SendRentReminder::class, function ($job) use ($inactiveLease) {
            return $job->lease->id === $inactiveLease->id;
        });
    }
}
