<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;
use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $modelLabel = 'Validasi Pembayaran';

    // Hanya bisa diakses oleh admin
    public static function canAccess(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_users')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->disabled(),
                Forms\Components\TextInput::make('nominal')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'reject' => 'Reject',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('unique_code')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nominal')
                    ->numeric(
                        decimalPlaces: 0, // Tidak menampilkan desimal
                        decimalSeparator: ',',
                        thousandsSeparator: '.'
                    ),

                Tables\Columns\TextColumn::make('unique_code')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'reject' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'reject' => 'Reject',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'reject' => 'Reject',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->successNotificationTitle('Status pembayaran berhasil diupdate!'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
