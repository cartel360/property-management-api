<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPaymentReceipt implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Payment $payment)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulate sending email by logging
        Log::info("Payment receipt sent for Payment ID: {$this->payment->id}", [
            'amount' => $this->payment->amount,
            'tenant' => $this->payment->lease->tenant->name,
            'date' => $this->payment->payment_date,
        ]);
    }
}
