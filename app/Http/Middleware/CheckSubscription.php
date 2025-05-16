<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            throw new \Exception('Authenticated user is not an instance of App\Models\User.');
        }

        // Jika belum login, redirect ke login
        if (!$user) {
            return redirect()->route('login');
        }

        // Jika tanggal aktif sudah lewat
        if ($user->active_until && Carbon::now()->greaterThan($user->active_until)) {
            if ($user->is_subscribed) {
                $user->update([
                    'is_subscribed' => false,
                ]);
            }
        }

        Payment::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'reject']);

        // Cek langganan
        if (!$user->is_subscribed) {
            $allowedRoutes = [
                'filament.admin.pages.dashboard',
                'filament.admin.auth.logout',
                'filament.admin.resources.payment-users.index',
                'filament.admin.resources.payment-users.view',
                'filament.admin.resources.payment-users.edit',
                'filament.admin.resources.payment-users.create',
            ];

            $currentRoute = $request->route()?->getName();

            if (!in_array($currentRoute, $allowedRoutes)) {
                Notification::make()
                    ->title('Langganan Tidak Aktif')
                    ->body('Langganan Anda sudah tidak aktif. Silakan perbarui untuk akses penuh.')
                    ->danger()
                    ->persistent()
                    ->send();

                return redirect()->route('filament.admin.pages.dashboard');
            }
        }

        return $next($request);
    }
}
