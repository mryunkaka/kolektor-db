<?php

namespace App\Filament\Resources\PaymentUserResource\Pages;

use App\Filament\Resources\PaymentUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentUsers extends ListRecords
{
    protected static string $resource = PaymentUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
