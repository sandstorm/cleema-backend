<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JoinedChallengesResource\Pages;
use App\Filament\Resources\JoinedChallengesResource\RelationManagers;
use App\Filament\Resources\QuizzesResource\RelationManagers\AnswersRelationManager;
use App\Models\Challenges;
use App\Models\JoinedChallenges;
use App\Models\UpUsers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JoinedChallengesResource extends Resource
{
    protected static ?string $model = JoinedChallenges::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->disabled()
                    ->numeric()
                    ->hiddenOn('create'),
                Forms\Components\Fieldset::make('User')
                    ->relationship('user')
                    ->schema([
                        Forms\Components\Select::make('username')
                            ->options(UpUsers::query()->pluck('username', 'id')->filter())
                            ->preload()
                            ->live()
                            ->searchable()
                            ->afterStateUpdated(fn(Set $set, $state) => $set('email', UpUsers::find($state)->email)),
                        Forms\Components\TextInput::make('email')
                            ->disabled(),
                    ]),
                Forms\Components\Select::make('challenge')
                    ->native(false)
                    ->searchable()
                    ->relationship('challenge')
                    ->options(Challenges::query()->pluck('title', 'id')->filter()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.username')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
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
            RelationManagers\AnswersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJoinedChallenges::route('/'),
            'create' => Pages\CreateJoinedChallenges::route('/create'),
            'edit' => Pages\EditJoinedChallenges::route('/{record}/edit'),
        ];
    }
}
