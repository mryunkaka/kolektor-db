<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class LoginCustom extends Login
{
    /**
     * Mendapatkan form untuk login.
     *
     * @return array
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getLoginFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data')
            ),
        ];
    }

    /**
     * Melakukan autentikasi user
     */
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        // Perbaikan validasi: cek apakah input adalah nomor telepon dengan regex
        $is_phone = preg_match('/^[0-9]+$/', $data['login']);
        $login_type = $is_phone ? 'phone' : 'name';

        $authenticated = Auth::attempt([
            $login_type => $data['login'],
            'password' => $data['password'],
        ], $data['remember'] ?? false);

        if (! $authenticated) {
            Notification::make()
                ->title('Login Gagal')
                ->body('Phone/nama atau password salah.')
                ->danger()
                ->send();

            $this->throwFailureValidationException();
        }
        $user = Auth::user();
        if ($user->active_until && $user->active_until->diffInDays(now()) <= 1 && $user->active_until->isFuture()) {
            Notification::make()
                ->title('Langganan Akan Kedaluwarsa')
                ->body('Langganan Anda akan berakhir pada ' . $user->active_until->format('d M Y H:i') . '.')
                ->warning()
                ->send();
        }


        return app(LoginResponse::class);
    }

    /**
     * Membuat komponen input untuk login (phone atau nama).
     *
     * @return Component
     */
    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('Phone / Nama'))
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * Mengambil kredensial dari data form.
     *
     * @param array $data
     * @return array
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        // Tentukan apakah input adalah phone atau nama dengan regex
        $is_phone = preg_match('/^[0-9]+$/', $data['login']);
        $login_type = $is_phone ? 'phone' : 'name';

        return [
            $login_type => $data['login'],
            'password' => $data['password'],
        ];
    }

    /**
     * Menangani validasi yang gagal.
     *
     * @throws ValidationException
     */
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
