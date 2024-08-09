<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\ImageUploader;
use App\Filament\Resources\Components\LocaleSelector;
use App\Filament\Resources\Components\LocationSelector;
use App\Filament\Resources\Components\PartnersSelector;
use App\Filament\Resources\Components\RegionsSelector;
use App\Filament\Resources\Components\RestrictedMarkdownEditor;
use App\Filament\Resources\ProjectsResource\Pages;
use App\Filament\Resources\ProjectsResource\RelationManagers;
use App\Models\Projects;
use App\Models\Regions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Fieldset;

class ProjectsResource extends Resource
{
    protected static ?string $model = Projects::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(6)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->columnSpan(2)
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('summary')
                            ->columnSpan(4)
                            ->required(),
                        RestrictedMarkdownEditor::getRestrictedMarkdownEditor('description'),
                        Forms\Components\Textarea::make('conclusion')
                            ->columnSpanFull()
                            ->rows(2),
                        Forms\Components\Select::make('goal_type')
                            ->columnSpan(2)
                            ->label(__("Goal Type"))
                            ->options([
                                "information" => "Information",
                                "involvement" => "Involvement"
                            ])
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('phase')
                            ->options([
                                'pre' => 'Preview',
                                'running' => 'Running',
                                'finished' => 'Finished'
                            ])
                            ->default('pre')
                            ->native(false)
                            ->columnSpan(2)
                            ->required(),
                        RegionsSelector::getRegionsSelector(),
                        PartnersSelector::getPartnersSelector(false, true),
                        Fieldset::make(__('Goal Involvements'))
                            ->relationship(name: 'goalInvolvement')
                            ->schema([
                                Forms\Components\TextInput::make('max_participants')
                                    ->label(__('Maximum Participants'))
                                    ->numeric(),
                                Forms\Components\TextInput::make('current_participants')
                                    ->label(__('Current Participants'))
                                    ->numeric()
                                    //->state(fn(Forms\Set $set, $state) => \DB::table('projects_users_joined'))
                                    ->disabled(),
                            ]),
                        ImageUploader::getImageUploader('image', 'projects','Main Image', true),
                        ImageUploader::getImageUploader('teaserImage', 'projects/teaser', 'Teaser Image', true),
                        LocationSelector::getLocationSelector(),
                        /*Forms\Components\FieldSet::make('Goal Funding')
                            ->relationship('goalFunding')
                            ->schema([
                                Forms\Components\TextInput::make('current_amount')
                                    ->label('Current Amount')
                                    ->disabled(),
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->disabled(),
                            ]),*/
                        Forms\Components\Section::make()
                            ->columns(6)
                            ->schema([
                                Forms\Components\DateTimePicker::make('start_date')
                                    ->columnSpan(2)
                                    ->required(),
                                // TODO: Projects currently have no end_date set -> instead of setting phase, it should be automatically set depending on when start and end date are relative to now
                                /*Forms\Components\DateTimePicker::make('end_date')
                                    ->columnSpan(2)
                                    ->required(),*/
                                DateSelectors::getPublishedAtDateSelector(),
                            ]),
                        LocaleSelector::getLocaleSelector(),
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('summary')
                    ->sortable()
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('phase')
                    ->badge()
                    ->getStateUsing(function (Projects $record){
                        if ($record->phase == NULL) $record->phase = 'undefined';
                        return match ($record->phase) {
                            'pre' => 'Preview',
                            'running' => 'Running',
                            'finished' => 'Finished',
                            'undefined' => 'undefined'
                        };
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Preview' => 'gray',
                        'Running' => 'success',
                        'Finished' => 'info',
                        'undefined' => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->searchable()
                    ->sortable()
                    ->label('Published At')
                    ->formatStateUsing(function (Projects $projects) {
                        $dateTime = new \DateTime($projects->published_at);
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
        return [
            RelationManagers\UsersFavoritedRelationManager::class,
            RelationManagers\UsersJoinedRelationManager::class,
            RelationManagers\RelatedProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProjects::route('/create'),
            'edit' => Pages\EditProjects::route('/{record}/edit'),
        ];
    }
}
