<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChallengesResource\Pages;
use App\Filament\Resources\ChallengesResource\RelationManagers;
use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\GoalTypeSelector;
use App\Filament\Resources\Components\ImageUploader;
use App\Filament\Resources\Components\IntervalSelector;
use App\Filament\Resources\Components\LocaleSelector;
use App\Filament\Resources\Components\PartnersSelector;
use App\Filament\Resources\Components\RegionsSelector;
use App\Filament\Resources\Components\RestrictedMarkdownEditor;
use App\Models\Challenges;
use App\Models\Partners;
use App\Models\Regions;
use App\Models\UpUsers;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChallengesResource extends Resource
{
    protected static ?string $model = Challenges::class;

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
                            ->maxLength(255)
                            ->label(__('Title'))
                            ->required(),
                        Forms\Components\TextInput::make('id')
                            ->disabled()
                            ->label(__('ID'))
                            ->numeric()
                            ->hiddenOn('create')
                            ->columnSpan(1),
                        RestrictedMarkdownEditor::getRestrictedMarkdownEditor('description', __('Description')),
                        Forms\Components\Textarea::make('teaser_text')
                            ->label('Teaser')
                            ->columnSpanFull()
                            ->rows(2.5)
                            ->maxLength(255),
                        IntervalSelector::getIntervalSelector('interval', 2, true),
                        RegionsSelector::getRegionsSelector(),
                        GoalTypeSelector::getGoalTypeSelector(true),
                        Forms\Components\Select::make('kind')
                            ->columnSpan(fn ($state) => $state == 'collective' ? 1 : 2)
                            ->label('Kind')
                            ->live()
                            ->options([
                                // user and group challenges are only created by Users via ChallengeTemplates
                                // 'user' => 'user',
                                // 'group' => 'group',
                                'partner' => 'partner',
                                'collective' => 'collective',
                                ])
                            ->native(false)
                            ->required()
                            ->hintIcon('heroicon-s-question-mark-circle')
                            ->hintIconTooltip('Partner: Challenge everyone can join to create their own personal user Challenge. -
                            Collective: Challenge everyone can join and contribute to the collective goal.'),
                        Forms\Components\TextInput::make('collective_goal_amount')
                            ->numeric()
                            ->minValue(1) //IDE throws, but it works and documentation agrees
                            ->hidden(fn (Get $get) => $get('kind') !== 'collective')
                            ->required(),
                        Forms\Components\Select::make('Author')
                            ->relationship('author')
                            ->options(UpUsers::query()
                                ->select([
                                    UpUsers::raw("CONCAT(username, ' - ', email) as user"),
                                    'id',
                                ])
                                ->pluck('user', 'id')->filter())
                            ->preload()
                            ->live()
                            ->searchable()
                            ->columnSpan(3),
                        Forms\Components\Toggle::make('trophy_processed')
                            ->columnSpan(1),
                        PartnersSelector::getPartnersSelector(function (Get $get) {
                            $isPartner = $get('kind') == 'partner';
                            $isCollective = $get('kind') == 'collective';
                            return !($isCollective || $isPartner);
                        }, true),
                        ImageUploader::getImageUploader('image', 'challenges', 'Image', true),
                        LocaleSelector::getLocaleSelector(),
                        Forms\Components\Section::make()
                            ->columns(6)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->columnSpan(2),
                                Forms\Components\DatePicker::make('end_date')
                                    ->columnSpan(2),
                                DateSelectors::getPublishedAtDateSelector(),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table, Bool $showUserChallenges = false): Table
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
                Tables\Columns\TextColumn::make('region_id')
                    ->label('Region')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => Regions::find($state)->name),
                Tables\Columns\TextColumn::make('locale')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('published_at')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (Challenges $challenges) {
                        $dateTime = new \DateTime($challenges->published_at);
                        $dateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
                        return $dateTime->format('Y-m-d H:i:s');
                    }),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('public')
                    ->label(__('Public Challenges'))
                    ->query(function (Builder $query) {
                        $query->whereIn('kind', ['partner', 'collective']);
                    })
                    ->default(),
                Tables\Filters\Filter::make('user')
                    ->label(__('User Challenges'))
                    ->query(function (Builder $query) {
                        $query->whereIn('kind', ['user', 'group']);
                    }),
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
            RelationManagers\JoinedUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChallenges::route('/'),
            'create' => Pages\CreateChallenges::route('/create'),
            'edit' => Pages\EditChallenges::route('/{record}/edit'),
        ];
    }
}
