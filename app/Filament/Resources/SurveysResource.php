<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\LocaleSelector;
use App\Filament\Resources\Components\RestrictedMarkdownEditor;
use App\Filament\Resources\SurveysResource\Pages;
use App\Filament\Resources\SurveysResource\RelationManagers;
use App\Models\Surveys;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SurveysResource extends Resource
{
    protected static ?string $model = Surveys::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(6)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->maxLength(255)
                            ->columnSpan(4),
                        Forms\Components\TextInput::make('id')
                            ->numeric()
                            ->columnSpan(1)
                            ->hiddenOn('create')
                            ->disabled(),
                        RestrictedMarkdownEditor::getRestrictedMarkdownEditor('description'),
                        Forms\Components\TextInput::make('survey_url')
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('evaluation_url')
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Select::make('target')
                            ->options([
                                'all' => 'All',
                            ])
                            ->columnSpan(2),
                        Forms\Components\Toggle::make('finished')
                            ->columnSpan(3),
                        Forms\Components\Toggle::make('trophy_processed')
                            ->columnSpan(3),
                        LocaleSelector::getLocaleSelector(),
                        DateSelectors::getPublishedAtDateSelector(),
                    ]),
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
                Tables\Columns\TextColumn::make('survey_url')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('evaluation_url')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('finished')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('locale')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(function (Surveys $surveys) {
                        $dateTime = new \DateTime($surveys->published_at);
                        $dateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
                        return $dateTime->format('Y-m-d H:i:s');
                    }),
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
            RelationManagers\ParticipantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurveys::route('/create'),
            'edit' => Pages\EditSurveys::route('/{record}/edit'),
        ];
    }
}
