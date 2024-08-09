<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationsResource\Pages;
use App\Models\Locations;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;

class LocationsResource extends Resource
{
    protected static ?string $model = Locations::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(6)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->columnSpan(5)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('id')
                            ->numeric()
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\Fieldset::make('Coordinates')
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->numeric(),
                                Forms\Components\TextInput::make('longitude')
                                    ->numeric(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocations::route('/create'),
            'edit' => Pages\EditLocations::route('/{record}/edit'),
        ];
    }
}
