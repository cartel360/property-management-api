<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendRentReminder;
use App\Models\Lease;

class SendRentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:rent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send rent due reminders to tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leases = Lease::where('status', 'active')
            ->with(['tenant', 'unit.property'])
            ->get();

        foreach ($leases as $lease) {
            SendRentReminder::dispatch($lease)
                ->delay(now()->addSeconds(5)); // Stagger reminders
        }

        $this->info("Sent rent reminders for {$leases->count()} leases");
    }
}
