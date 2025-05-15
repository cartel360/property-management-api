<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Lease;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leases = Lease::all();

        foreach ($leases as $lease) {
            $startDate = Carbon::parse($lease->start_date);
            $endDate = Carbon::parse($lease->end_date);

            $months = $startDate->diffInMonths($endDate);
            $months = min($months, 5); // Limit to 5 months for each lease

            for ($i = 0; $i < $months; $i++) {
                $paymentDate = $startDate->copy()->addMonths($i);

                Payment::create([
                    'lease_id' => $lease->id,
                    'amount' => $lease->monthly_rent,
                    'payment_date' => $paymentDate,
                    'payment_method' => ['cash', 'cheque', 'bank transfer', 'credit card'][rand(0, 3)],
                    'status' => $paymentDate->isPast() ? 'completed' : 'pending',
                ]);
            }
        }
    }
}
