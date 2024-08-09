<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UpUsersResource\Pages;
use App\Filament\Resources\UpUsersResource\RelationManagers;
use App\Models\UpUsers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UpUsersResource extends Resource
{
    protected static ?string $model = UpUsers::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('username')
                    ->maxLength(255),
                Forms\Components\Toggle::make('blocked'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->label('Email-address')
                    ->maxLength(255),
                Forms\Components\Toggle::make('confirmed'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\TextInput::make('uuid')
                    ->disabled()
                    ->maxLength(255)
                    ->uuid(),
                Forms\Components\TextInput::make('referral_code')
                    ->disabled(),
                Forms\Components\Toggle::make('accepts_surveys'),
                Forms\Components\Fieldset::make('Avatar')
                    ->relationship('avatar')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                        ->numeric()
                        ->disabled()
                        ->label('Avatar Id'),
                    ]),
                Forms\Components\Fieldset::make('Quiz-Streak')
                    ->relationship('quizStreak')
                    ->schema([
                        Forms\Components\TextInput::make('correct_answer_streak')
                            ->numeric()
                            ->disabled()
                            ->label('Correct Answer Streak'),
                        Forms\Components\TextInput::make('participation_streak')
                            ->numeric()
                            ->label('Participation Streak')
                            ->registerListeners([

                            ])
                            ->disabled(),
                    ]),
                Forms\Components\Fieldset::make('Region')
                    ->relationship('region')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->numeric()
                            ->disabled()
                            ->label('Region Id'),
                        Forms\Components\TextInput::make('name')
                            ->label('Region Name')
                            ->disabled(),
                    ]),
                Forms\Components\Fieldset::make('Role')
                    ->relationship('role')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->disabled()
                            ->label('Role Name'),
                        Forms\Components\TextInput::make('description')
                            ->label('Role Description')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('confirmed')
                    ->boolean(),
            ])
            ->filters([
                //
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
            RelationManagers\FollowsRelationManager::class,
            RelationManagers\TrophiesRelationManager::class,
            RelationManagers\QuizResponsesRelationManager::class,
            RelationManagers\FavoritedNewsEntriesRelationManager::class,
            RelationManagers\AuthoredChallengesRelationManager::class,
            RelationManagers\ChallengesJoinedRelationManager::class,
            RelationManagers\ProjectsFavoritedRelationManager::class,
            RelationManagers\ProjectsJoinedRelationManager::class,
            RelationManagers\EnteredSurveysRelationManager::class,
            RelationManagers\ReadNewsEntriesRelationManager::class,
            RelationManagers\VoucherRedemptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUpUsers::route('/'),
            'create' => Pages\CreateUpUsers::route('/create'),
            'edit' => Pages\EditUpUsers::route('/{record}/edit'),
        ];
    }
}
