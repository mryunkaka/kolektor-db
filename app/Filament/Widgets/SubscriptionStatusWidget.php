<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SubscriptionStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.subscription-status-widget';

    protected static ?int $sort = 2;

    // Pastikan widget memuat ulang datanya saat dimuat
    protected static bool $isLazy = true;

    public function getHeading(): string
    {
        return 'Status Langganan';
    }

    protected function getFooter(): ?string
    {
        $user = \App\Models\User::query()->find(Auth::id());

        if (!$user || !$user->active_until) {
            return 'Tidak aktif';
        }

        $now = now();
        $activeUntil = $user->active_until;

        if ($now->lt($activeUntil)) {
            $parts = [];

            $totalDays = $now->diffInDays($activeUntil);
            $diff = $now->diff($activeUntil);

            if ($totalDays > 0) {
                $parts[] = round($totalDays) . ' hari';
            }

            if ($diff->h > 0) {
                $parts[] = $diff->h . ' jam';
            }

            if ($diff->i > 0) {
                $parts[] = $diff->i . ' menit';
            }

            if (empty($parts) && $diff->s > 0) {
                $parts[] = $diff->s . ' detik';
            }

            $timeLeft = implode(' ', $parts);

            return "Aktif tersisa: {$timeLeft} (hingga {$activeUntil->format('d M Y H:i')})";
        }

        return 'Tidak aktif';
    }
}
