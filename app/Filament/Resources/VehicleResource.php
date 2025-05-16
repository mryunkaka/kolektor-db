<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Vehicle;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VehicleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Kainiklas\FilamentScout\Traits\InteractsWithScout;
use App\Filament\Resources\VehicleResource\RelationManagers;

class VehicleResource extends Resource
{
    use InteractsWithScout;

    protected static ?string $model = Vehicle::class;
    protected static ?string $modelLabel = 'Vehicle';
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $recordTitleAttribute = 'no_polisi';
    protected static ?string $navigationLabel = 'Vehicles';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_kontrak')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('nama_konsumen')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('no_polisi')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('no_rangka')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('no_mesin')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('merk_tipe')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('past_due')
                    ->numeric(),
                Forms\Components\TextInput::make('nama_resort')
                    ->maxLength(100),
                Forms\Components\TextInput::make('nama_sector')
                    ->maxLength(100),
                Forms\Components\TextInput::make('nama_sub_sector')
                    ->maxLength(100),
                Forms\Components\TextInput::make('product')
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_polisi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_konsumen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('merk_tipe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_kontrak')
                    ->searchable(),
                Tables\Columns\TextColumn::make('past_due')
                    ->numeric(),
                Tables\Columns\TextColumn::make('nama_resort'),
                Tables\Columns\TextColumn::make('nama_sector'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('nama_resort')
                    ->options(
                        Vehicle::query()->pluck('nama_resort', 'nama_resort')->unique()
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn() => Auth::user()->role === 'user'),
                Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            // 'edit' => Pages\EditVehicle::route('/{record}/edit'),
            // 'view' => Pages\ViewVehicle::route('/{record}'),
        ];
    }
}
