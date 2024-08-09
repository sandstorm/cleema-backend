<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChallengeTemplatesResource\Pages;
use App\Filament\Resources\ChallengeTemplatesResource\RelationManagers;
use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\GoalTypeSelector;
use App\Filament\Resources\Components\ImageUploader;
use App\Filament\Resources\Components\IntervalSelector;
use App\Filament\Resources\Components\LocaleSelector;
use App\Filament\Resources\Components\PartnersSelector;
use App\Filament\Resources\Components\RestrictedMarkdownEditor;
use App\Models\Challenges;
use App\Models\ChallengeTemplates;
use App\Models\Partners;
use App\Models\Regions;
use App\Models\UpUsers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChallengeTemplatesResource extends Resource
{
    protected static ?string $model = ChallengeTemplates::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(6)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->columnSpanFull()
                            ->columnSpan(5)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('id')
                            ->disabled()
                            ->numeric()
                            ->hiddenOn('create')
                            ->columnSpan(1),
                        RestrictedMarkdownEditor::getRestrictedMarkdownEditor('description'),
                        Forms\Components\Textarea::make('teaser_text')
                            ->label('Teaser')
                            ->columnSpanFull()
                            ->rows(2.5)
                            ->maxLength(255),
                        IntervalSelector::getIntervalSelector('interval'),
                        GoalTypeSelector::getGoalTypeSelector(),
                        Forms\Components\Select::make('kind')
                            ->columnSpan(2)
                            ->label('Kind')
                            ->live()
                            ->options(
                                ChallengeTemplates::query()->distinct()->pluck('kind', 'kind')->filter()
                            )
                            ->native(false),
                        PartnersSelector::getPartnersSelector(),
                        ImageUploader::getImageUploader('image', 'challenges'),
                        DateSelectors::getPublishedAtDateSelector(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('goal_type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kind')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (ChallengeTemplates $challengeTemplates) {
                        $dateTime = new \DateTime($challengeTemplates->published_at);
                        $dateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
                        return $dateTime->format('Y-m-d H:i:s');
                    }),
            ])
            ->defaultSort('published_at', 'desc')
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
            'index' => Pages\ListChallengeTemplates::route('/'),
            'create' => Pages\CreateChallengeTemplates::route('/create'),
            'edit' => Pages\EditChallengeTemplates::route('/{record}/edit'),
        ];
    }
}
