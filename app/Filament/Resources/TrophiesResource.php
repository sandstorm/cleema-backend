<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\ImageUploader;
use App\Filament\Resources\TrophiesResource\Pages;
use App\Filament\Resources\TrophiesResource\RelationManagers;
use App\Models\Challenges;
use App\Models\Trophies;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrophiesResource extends Resource
{
    protected static ?string $model = Trophies::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\TextInput::make('id')
                    ->numeric()
                    ->disabled()
                    ->hiddenOn('create'),
                Forms\Components\Select::make('kind')
                    ->options([
                        'account-age' => 'Account Age',
                        'challenge-participation' => 'Challenge Participation',
                        'challenge-partner' => 'Challenge Partner',
                        'project-participation' => 'Project Participation',
                        'quiz-correct-answers' => 'Correct Quiz Answer',
                        'referral-count' => 'Referral Count',
                        'survey-participation' => 'Survey Participation',
                    ])
                    ->live()
                    ->searchable(),
                Forms\Components\TextInput::make('amount')
                    ->numeric(),
                Forms\Components\Select::make('Challenge')
                    ->relationship('challenge')
                    ->hidden(fn(Get $get) => $get('kind') !== 'challenge-participation' && $get('kind') !== 'challenge-partner')
                    ->label('Challenge Title')
                    ->options(Challenges::query()
                        ->select([
                            Challenges::raw("CONCAT(id, ' - ', title) as challengeName"),
                            'id',
                        ])
                        ->pluck('challengeName', 'id')->filter())
                    ->preload()
                    ->searchable(),
                ImageUploader::getImageUploader('image', 'trophies'),
                Forms\Components\Select::make('locale')
                    ->options([
                        'de-DE' => 'de-DE',
                    ])
                    ->default('de-DE'),
                DateSelectors::getPublishedAtDateSelector(1),
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
                Tables\Columns\TextColumn::make('kind')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('locale')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(function (Trophies $trophies) {
                        $dateTime = new \DateTime($trophies->published_at);
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrophies::route('/'),
            'create' => Pages\CreateTrophies::route('/create'),
            'edit' => Pages\EditTrophies::route('/{record}/edit'),
        ];
    }
}
