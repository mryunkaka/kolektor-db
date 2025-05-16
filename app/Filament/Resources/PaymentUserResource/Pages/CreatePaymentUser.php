<?php

namespace App\Filament\Resources\PaymentUserResource\Pages;

use App\Filament\Resources\PaymentUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentUser extends CreateRecord
{
    protected static string $resource = PaymentUserResource::class;
}
