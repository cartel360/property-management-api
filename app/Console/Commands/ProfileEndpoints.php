<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Lease;

class ProfileEndpoints extends Command
{
    protected $signature = 'profile:endpoints
                           {--user=1 : User ID to authenticate}
                           {--iterations=3 : Number of test iterations}';
    protected $description = 'Automatically profile all API endpoints';

    public function handle()
    {
        $user = \App\Models\User::find($this->option('user'));

        // Check if user is found
        if (!$user) {
            $this->error('No query results for model'); // Custom error message
            return 1; // Exit with failure code
        }

        $token = $user->createToken('profiling-token')->plainTextToken;

        $property = Property::first();
        $unit = Unit::first();
        $tenant = Tenant::first();
        $lease = Lease::first();

        $endpoints = [
            // Auth Endpoints
            'POST /api/v1/login' => ['url' => '/api/v1/login', 'method' => 'post', 'data' => ['email' => $user->email, 'password' => 'password']],
            'POST /api/v1/register' => ['url' => '/api/v1/register', 'method' => 'post', 'data' => ['email' => $user->email, 'password' => 'password', 'password_confirmation' => 'password', 'name' => Str::random(10)]],

            // Leases Endpoints
            'GET /api/v1/leases' => '/api/v1/leases',
            'POST /api/v1/leases' => [
                'url' => '/api/v1/leases',
                'method' => 'post',
                'data' => [
                    'unit_id' => $unit->id,
                    'tenant_id' => $tenant->id,
                    'start_date' => now(),
                    'end_date' => now()->addYear(),
                    'monthly_rent' => 1000.00,
                    'security_deposit' => 500.00,
                    'status' => 'active'
                ]
            ],

            // Payments Endpoints
            'GET /api/v1/payments' => '/api/v1/payments',
            'POST /api/v1/payments' => [
                'url' => '/api/v1/payments',
                'method' => 'post',
                'data' => [
                    'lease_id' => $lease->id,
                    'amount' => 500.00,
                    'payment_date' => now(),
                    'payment_method' => 'credit_card',
                    'transaction_reference' => 'TXN123456',
                    'status' => 'completed'
                ]
            ],

            // Properties Endpoints
            'GET /api/v1/properties' => '/api/v1/properties',
            'POST /api/v1/properties' => [
                'url' => '/api/v1/properties',
                'method' => 'post',
                'data' => [
                    'landlord_id' => $user->id,
                    'name' => 'Test Property',
                    'address' => '123 Nairobi St',
                    'city' => 'Nairobi',
                    'state' => 'NBO',
                    'zip_code' => '10001'
                ]
            ],

            // Tenants Endpoints
            'GET /api/v1/tenants' => '/api/v1/tenants',
            'POST /api/v1/tenants' => [
                'url' => '/api/v1/tenants',
                'method' => 'post',
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'phone' => '555-555-5555',
                    'address' => '456 Koinange St',
                    'date_of_birth' => now()->subYears(25),
                    'emergency_contact_name' => 'Jane Doe',
                    'emergency_contact_phone' => '555-555-5555'
                ]
            ],

            // Units Endpoints
            'GET /api/v1/properties/{propertyId}/units' => '/api/v1/properties/{propertyId}/units',
            'POST /api/v1/properties/{propertyId}/units' => [
                'url' => '/api/v1/properties/{propertyId}/units',
                'method' => 'post',
                'data' => function ($property) {
                    return [
                        'unit_number' => 'A' . rand(100, 999),
                        'rent_amount' => rand(1000, 3000),
                        'size' => rand(500, 1000),
                        'bedrooms' => rand(1, 3),
                        'bathrooms' => rand(1, 2),
                        'features' => ['Balcony', 'Air Conditioning', 'Parking'],
                        'status' => 'vacant'
                    ];
                }
            ],
        ];

        $results = [];

        foreach ($endpoints as $name => $config) {
            // If the data is a closure, call it and pass the property object to generate data
            if (isset($config['data']) && is_callable($config['data'])) {
                $config['data'] = $config['data']($property);
            }

            $results[$name] = $this->profileEndpoint(
                is_array($config) ? $config['url'] : $config,
                $token,
                is_array($config) ? $config : ['method' => 'get']
            );
        }

        $this->generateReport($results);
    }

    protected function profileEndpoint($url, $token, $options = [])
    {
        $method = $options['method'] ?? 'get';
        $data = $options['data'] ?? [];

        $times = [];
        $memory = [];

        for ($i = 0; $i < $this->option('iterations'); $i++) {
            // Replace the placeholder in URL with actual propertyId if needed
            if (strpos($url, '{propertyId}') !== false) {
                $propertyId = 1;  // Replace with actual logic to fetch propertyId
                $url = str_replace('{propertyId}', $propertyId, $url);
            }

            // Handle dynamic email generation if needed
            if (isset($data['email'])) {
                $randomEmail = Str::random(10) . '@example.com';
                $data['email'] = $randomEmail;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json',
                'X-Telescope' => 'true',
            ])->{$method}("http://localhost:8000{$url}", $data);

            // $times[] = $response->handlerStats()['total_time'] * 1000; // ms
            $handlerStats = $response->handlerStats();
            $times[] = isset($handlerStats['total_time']) ? $handlerStats['total_time'] * 1000 : 0; // ms
            $memory[] = memory_get_peak_usage(true) / 1024 / 1024; // MB
        }

        return [
            'avg_time' => array_sum($times) / count($times),
            'avg_memory' => array_sum($memory) / count($memory),
            'telescope_url' => "http://localhost:8000/telescope/requests?filter[uri]=$url"
        ];
    }

    protected function generateReport($results)
    {
        $report = "# API Performance Report\n\n";
        $report .= "Generated on: " . now()->toDateTimeString() . "\n\n";

        foreach ($results as $endpoint => $metrics) {
            $report .= "## $endpoint\n";
            $report .= "- Average Time: {$metrics['avg_time']}ms\n";
            $report .= "- Average Memory: {$metrics['avg_memory']}MB\n";
            $report .= "- [Telescope Details]({$metrics['telescope_url']})\n\n";

            // Auto-generate improvement suggestions
            if ($metrics['avg_time'] > 500) {
                $report .= "### Suggested Improvements\n";
                $report .= "- Investigate slow queries in Telescope\n";
                $report .= "- Consider adding caching\n";
                $report .= "- Check for N+1 problems\n\n";
            }
        }

        // Start HTML Report
        $htmlReport = '<html><head><title>API Performance Report</title></head><body>';
        $htmlReport .= '<h1>API Performance Report</h1>';
        $htmlReport .= '<p>Generated on: ' . now()->toDateTimeString() . '</p>';

        foreach ($results as $endpoint => $metrics) {
            $report .= "## $endpoint\n";
            $report .= "- Average Time: {$metrics['avg_time']}ms\n";
            $report .= "- Average Memory: {$metrics['avg_memory']}MB\n";
            $report .= "- [Telescope Details]({$metrics['telescope_url']})\n\n";

            // Add the same data to the HTML report
            $htmlReport .= "<h2>$endpoint</h2>";
            $htmlReport .= "<ul>";
            $htmlReport .= "<li><strong>Average Time:</strong> {$metrics['avg_time']}ms</li>";
            $htmlReport .= "<li><strong>Average Memory:</strong> {$metrics['avg_memory']}MB</li>";
            $htmlReport .= "<li><a href='{$metrics['telescope_url']}'>Telescope Details</a></li>";

            // Auto-generate improvement suggestions
            if ($metrics['avg_time'] > 500) {
                $report .= "### Suggested Improvements\n";
                $report .= "- Investigate slow queries in Telescope\n";
                $report .= "- Consider adding caching\n";
                $report .= "- Check for N+1 problems\n\n";

                // Add to HTML suggestions
                $htmlReport .= "<h3>Suggested Improvements</h3>";
                $htmlReport .= "<ul>";
                $htmlReport .= "<li>Investigate slow queries in Telescope</li>";
                $htmlReport .= "<li>Consider adding caching</li>";
                $htmlReport .= "<li>Check for N+1 problems</li>";
                $htmlReport .= "</ul>";
            }
            $htmlReport .= "</ul>";
        }

        // Close HTML tags
        $htmlReport .= '</body></html>';

        // Save the report to the storage
        Storage::put('reports/performance-' . now()->format('Y-m-d') . '.md', $report);
        Storage::put('reports/performance-' . now()->format('Y-m-d') . '.html', $htmlReport);

    }
}
