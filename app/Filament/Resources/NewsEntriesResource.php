<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\ImageUploader;
use App\Filament\Resources\Components\LocaleSelector;
use App\Filament\Resources\Components\NewsTagsCreation;
use App\Filament\Resources\Components\RegionsSelector;
use App\Filament\Resources\Components\RestrictedMarkdownEditor;
use App\Filament\Resources\NewsEntriesResource\Pages;
use App\Filament\Resources\NewsEntriesResource\RelationManagers;
use App\Models\NewsEntries;
use App\Models\NewsTags;
use App\Models\Regions;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsEntriesResource extends Resource
{
    protected static ?string $model = NewsEntries::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(8)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->columnSpan(5)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('views')
                            ->disabled()
                            ->numeric()
                            ->hiddenOn('create')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('uniqueViews')
                            ->label(__('Unique Views'))
                            ->disabled()
                            ->numeric()
                            ->hiddenOn('create')
                            ->columnSpan(1)
                            ->formatStateUsing(fn(NewsEntries $entry) => count($entry->usersRead()->get())),
                        Forms\Components\TextInput::make('id')
                            ->disabled()
                            ->hiddenOn('create')
                            ->columnSpan(1),
                        RestrictedMarkdownEditor::getRestrictedMarkdownEditor('description'),
                        Forms\Components\Textarea::make('teaser')
                            ->columnSpanFull()
                            ->rows(2.5),
                        Forms\Components\Select::make('type')
                            ->options([
                                'tip' => 'Tip',
                                'news' => 'News',
                            ])
                            ->searchable()
                            ->columnSpan(3),
                        RegionsSelector::getRegionsSelector(3),
                        Forms\Components\Select::make('Tags')
                            ->multiple()
                            ->relationship('tags', 'value')
                            ->createOptionForm(NewsTagsCreation::getModal())
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                        ImageUploader::getImageUploader('image', 'news_entries', 'Image',true),
                        Forms\Components\Section::make()
                            ->columns(4)
                            ->schema([
                                DateSelectors::getPublishedAtDateSelector(),
                                DatePicker::make('date')
                                    ->columnSpan(2)
                                    ->hintIcon('heroicon-s-question-mark-circle')
                                    ->hintIconTooltip('The date shown in the app, on default identical to publishing date.')
                                    ->default(date('Y-m-d')),
                            ]),
                        LocaleSelector::getLocaleSelector(2),
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
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('region_id')
                    ->label('Region')
                    ->formatStateUsing(function ($state) {
                        return Regions::find($state)->name;
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usersRead')
                    ->label(__('Unique Views'))
                    ->formatStateUsing(fn(NewsEntries $entry) => count($entry->usersRead()->get()))
                    ->sortable(),
                Tables\Columns\TextColumn::make('views')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('locale')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('published_at')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (NewsEntries $newsEntry) {
                        $dateTime = new \DateTime($newsEntry->published_at);
                        $dateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
                        return $dateTime->format('Y-m-d H:i:s');
                    }),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters(
                self::getTableFiltersByTag()
            )
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return Tables\Filters\Filter[]
     * @throws Exception
     */
    public static function getTableFiltersByTag(): array
    {
        $filters = [];
        $news_tags = NewsTags::query()->distinct()->pluck('value', 'id');
        foreach ($news_tags as $key => $tagName) {
            $filter = Tables\Filters\Filter::make($tagName)
                ->label($tagName)
                ->query(function (Builder $query) use ($key) {
                    $query->whereHas('tags', function (Builder $query) use ($key) {
                        $query->where('news_tags.id', $key);
                    });
                });
            $filters[$tagName] = $filter;
        }
        return $filters;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersReadRelationManager::class,
            RelationManagers\UsersFavoritedRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsEntries::route('/'),
            'create' => Pages\CreateNewsEntries::route('/create'),
            'edit' => Pages\EditNewsEntries::route('/{record}/edit'),
        ];
    }
}
