<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Lease;
use Carbon\Carbon;

class LeaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = Unit::all();
        $tenants = Tenant::all();

        foreach ($units as $unit) {
            if (rand(0, 1)) { // 50% chance of being leased
                $startDate = Carbon::now()->subMonths(rand(0, 12));
                $endDate = $startDate->copy()->addMonths(rand(6, 24));

                Lease::create([
                    'unit_id' => $unit->id,
                    'tenant_id' => $tenants->random()->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'monthly_rent' => $unit->rent_amount,
                    'security_deposit' => $unit->rent_amount * 1.5,
                    'status' => $endDate->isFuture() ? 'active' : 'ended',
                ]);
            }
        }
    }
}
