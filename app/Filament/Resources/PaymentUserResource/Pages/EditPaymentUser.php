<?php

namespace App\Filament\Resources\PaymentUserResource\Pages;

use App\Filament\Resources\PaymentUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentUser extends EditRecord
{
    protected static string $resource = PaymentUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
