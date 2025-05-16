<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PaymentUser;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentUserResource\Pages;
use App\Filament\Resources\PaymentUserResource\RelationManagers;

class PaymentUserResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    // Hanya tampilkan data user yang login (untuk role user)
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->role === 'user') {
            return $query->where('id_users', Auth::id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('id_users')
                    ->default(Auth::id())
                    ->required(),

                Forms\Components\Select::make('duration')
                    ->options([
                        '1_day' => '1 Hari (Rp 10.000)',
                        '7_days' => '7 Hari (Rp 50.000)',
                        '30_days' => '30 Hari (Rp 100.000)',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $amount = match ($state) {
                            '1_day' => 10000,
                            '7_days' => 50000,
                            '30_days' => 100000,
                            default => 0,
                        };

                        // Generate unique code
                        do {
                            $uniqueCode = rand(1, 499);
                        } while (
                            Payment::where('unique_code', $uniqueCode)
                            ->where('status', 'pending')
                            ->exists()
                        );

                        $total = $amount + $uniqueCode;

                        $set('amount', $amount);
                        $set('unique_code', $uniqueCode);
                        $set('nominal', $total);
                        $set('id_users', Auth::id());
                    }),

                Forms\Components\TextInput::make('unique_code')
                    ->numeric()
                    ->readonly(),

                Forms\Components\TextInput::make('nominal')
                    ->numeric()
                    ->prefix('Rp')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.'))
                    ->dehydrateStateUsing(fn($state) => str_replace('.', '', $state))
                    ->readOnly(),

                Forms\Components\Select::make('bank_destination')
                    ->label('Bank Destination / E-Wallet')
                    ->options([
                        'BRI' => 'BRI - 453501039880535 (LISA RAHAYU)',
                        'BCA' => 'BCA - 7895865079 (LISA RAHAYU)',
                        'DANA' => 'DANA - 085642932839 (LISA RAHAYU)',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('duration')
                    ->formatStateUsing(fn($state) => match ($state) {
                        '1_day' => '1 Hari',
                        '7_days' => '7 Hari',
                        '30_days' => '30 Hari',
                    }),

                Tables\Columns\TextColumn::make('nominal')
                    ->numeric(
                        decimalPlaces: 0, // Tidak menampilkan desimal
                        decimalSeparator: ',',
                        thousandsSeparator: '.'
                    ),

                Tables\Columns\TextColumn::make('unique_code'),

                Tables\Columns\TextColumn::make('bank_destination'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'reject' => 'danger',
                        default => 'Status tidak dikenal',
                    }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make()
                //     ->visible(fn($record) => $record->status === 'pending'),
            ]);
    }

    protected function getTablePollingInterval(): ?string
    {
        return '10s'; // Refresh setiap 10 detik
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentUsers::route('/'),
            'create' => Pages\CreatePaymentUser::route('/create'),
        ];
    }
}
