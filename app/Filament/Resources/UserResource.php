<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'Pengguna';
    protected static ?string $navigationLabel = 'Manajemen Pengguna';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'admin' => 'Admin',
                                'user' => 'User',
                                'collector' => 'Kolektor'
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Status Langganan')
                    ->schema([
                        Forms\Components\Toggle::make('is_subscribed')
                            ->label('Status Berlangganan')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\DateTimePicker::make('active_until')
                            ->label('Aktif Hingga')
                            ->timezone('Asia/Singapore')
                            ->displayFormat('d M Y H:i')
                            ->native(false),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'danger',
                        'collector' => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\IconColumn::make('is_subscribed')
                    ->label('Berlangganan')
                    ->boolean(),

                Tables\Columns\TextColumn::make('active_until')
                    ->label('Aktif Hingga')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Singapore')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter Role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                        'collector' => 'Kolektor'
                    ]),

                Tables\Filters\Filter::make('is_subscribed')
                    ->label('Berlangganan Aktif')
                    ->query(fn(Builder $query): Builder => $query->where('is_subscribed', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\PaymentsRelationManager::class,
            // RelationManagers\VehiclesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
