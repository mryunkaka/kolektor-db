<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;

class RejectExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:reject-expired';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Auto reject pending payments after 10 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredPayments = Payment::where('status', 'pending')
            ->where(function ($query) {
                $query->where('expires_at', '<=', now())
                    ->orWhere('created_at', '<=', now()->subMinutes(10));
            })
            ->get();

        $expiredPayments->each->update(['status' => 'rejected']);

        $this->info("Rejected {$expiredPayments->count()} payments.");
    }
}
