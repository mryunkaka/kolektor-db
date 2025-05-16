<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PaymentResource;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    protected function beforeSave(): void
    {
        $status = $this->data['status'];
        $user = $this->record->user;

        // Kirim notifikasi ke user
        Notification::make()
            ->title("Pembayaran {$status}")
            ->body("Pembayaran Rp {$this->record->nominal} telah {$status} oleh admin.")
            ->sendToDatabase($user);
    }
}
