<?php

namespace App\Observers;

use App\Models\Payment;
use Illuminate\Support\Carbon;

class PaymentObserver
{
    public function updated(Payment $payment): void
    {
        // Hanya proses jika status berubah menjadi approved
        if ($payment->isDirty('status') && $payment->status === 'approved') {
            $user = $payment->user; // relasi harus ada di Payment model

            // Durasi dalam hari
            $days = match ($payment->duration) {
                '1_day' => 1,
                '7_days' => 7,
                '30_days' => 30,
                default => 0,
            };

            if ($days > 0 && $user) {
                $now = Carbon::now();

                $activeUntil = $user->active_until && $user->active_until->greaterThan($now)
                    ? $user->active_until->copy()->addDays($days)
                    : $now->copy()->addDays($days);

                $user->update([
                    'active_until' => $activeUntil,
                    'is_subscribed' => true,
                ]);
            }
        }
    }
}
