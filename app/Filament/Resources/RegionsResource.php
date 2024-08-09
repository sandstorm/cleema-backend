<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionsResource\Pages;
use App\Filament\Resources\RegionsResource\RelationManagers\ChallengesRelationManager;
use App\Filament\Resources\RegionsResource\RelationManagers\NewsEntriesRelationManager;
use App\Filament\Resources\RegionsResource\RelationManagers\OffersRelationManager;
use App\Filament\Resources\RegionsResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\RegionsResource\RelationManagers\UsersRelationManager;
use App\Models\Regions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;

class RegionsResource extends Resource
{
    protected static ?string $model = Regions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(8)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->columnSpan(4)
                    ->required(),
                Forms\Components\Toggle::make('is_public')
                    ->label(__('Visible'))
                    ->columnSpan(1)
                    ->required(),
                Forms\Components\Toggle::make('is_supraregional')
                    ->label(__('Supraregional'))
                    ->columnSpan(1)
                    ->disabled(function () {
                        $isSuperAdmin = auth()->user()->hasRole('super_admin');
                        if (!$isSuperAdmin) return true;
                        return false;
                    })
                    ->hintIcon('heroicon-s-question-mark-circle')
                    ->hintIconTooltip('There should only be one supraregional region at a time. Only Super Admins can set this property.')
                    ->required(),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
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
        return [
            UsersRelationManager::class,
            ChallengesRelationManager::class,
            OffersRelationManager::class,
            ProjectsRelationManager::class,
            NewsEntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegions::route('/create'),
            'edit' => Pages\EditRegions::route('/{record}/edit'),
        ];
    }
}
