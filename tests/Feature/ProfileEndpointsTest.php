<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Lease;
use App\Models\Property;

class ProfileEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for authentication
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Create a landlord user
        $landlord = User::factory()->create(['role' => 'landlord']);

        // Create a Property first so that it can be used in the Unit factory
        $property = Property::factory()->create();
        Tenant::factory()->create();
        Unit::factory()->create();
        Lease::factory()->create();

    }

    /** @test */
    public function it_can_run_the_profile_endpoints_command()
    {
        // Mocking the HTTP responses for profiling
        Http::fake([
            '*' => Http::response(['data' => 'test'], 200),
        ]);

        // Mock the handlerStats method to return fake stats
        Http::fake(function ($request) {
            return Http::response([], 200, [
                'X-Telescope' => 'true',
            ]);
        });

        // Run the command with the default options (user = 1, iterations = 3)
        $this->artisan('profile:endpoints')
            ->assertExitCode(0); // Ensure the command runs successfully

        // Check if the reports are saved to storage
        $this->assertTrue(Storage::exists('reports/performance-' . now()->format('Y-m-d') . '.md'));
        $this->assertTrue(Storage::exists('reports/performance-' . now()->format('Y-m-d') . '.html'));
    }




    /** @test */
    public function it_profiles_endpoints_with_custom_user_and_iterations()
    {
        // Mocking the HTTP responses for profiling
        Http::fake([
            '*' => Http::response(['data' => 'test'], 200),
        ]);

        // Run the command with custom user and iterations
        $this->artisan('profile:endpoints', [
            '--user' => $this->admin->id,
            '--iterations' => 5,
        ])->assertExitCode(0);

        // Check if the reports are saved to storage
        $this->assertTrue(Storage::exists('reports/performance-' . now()->format('Y-m-d') . '.md'));
        $this->assertTrue(Storage::exists('reports/performance-' . now()->format('Y-m-d') . '.html'));
    }


    /** @test */
    public function it_should_handle_invalid_user_option()
    {
        // Run the command with an invalid user ID
        $this->artisan('profile:endpoints', ['--user' => 999])
            ->assertExitCode(1) // Command should fail
            ->expectsOutput('No query results for model'); // Assert that the error message is shown
    }
}
