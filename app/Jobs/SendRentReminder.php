<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Lease;
use Illuminate\Support\Facades\Log;

class SendRentReminder implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Lease $lease)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulate sending reminder by logging
        Log::info("Rent reminder sent for Tenant: {$this->lease->tenant->name}", [
            'property' => $this->lease->unit->property->name,
            'unit' => $this->lease->unit->unit_number,
            'amount_due' => $this->lease->monthly_rent,
            'due_date' => now()->addDays(5)->toDateString(), // Assuming rent is due in 5 days
        ]);
    }
}
