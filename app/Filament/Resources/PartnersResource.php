<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\ImageUploader;
use App\Filament\Resources\Components\RestrictedMarkdownEditor;
use App\Filament\Resources\PartnersResource\Pages;
use App\Filament\Resources\PartnersResource\RelationManagers;
use App\Models\Partners;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PartnersResource extends Resource
{
    protected static ?string $model = Partners::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(7)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->maxLength(255)
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('url')
                            ->maxLength(255)
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('id')
                            ->numeric()
                            ->disabled()
                            ->hiddenOn('create')
                            ->columnSpan(1),
                        RestrictedMarkdownEditor::getRestrictedMarkdownEditor('description'),
                        ImageUploader::getImageUploader('logo', 'partners'),
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
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->searchable()
                    ->sortable()
                    ->label('Published At')
                    ->formatStateUsing(function (Partners $partners) {
                        $dateTime = new \DateTime($partners->published_at);
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
            RelationManagers\ProjectsRelationManager::class,
            RelationManagers\ChallengesRelationManager::class,
            RelationManagers\ChallengeTemplatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartners::route('/create'),
            'edit' => Pages\EditPartners::route('/{record}/edit'),
        ];
    }
}
