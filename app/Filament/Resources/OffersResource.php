<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Components\DateSelectors;
use App\Filament\Resources\Components\ImageUploader;
use App\Filament\Resources\Components\IntervalSelector;
use App\Filament\Resources\Components\LocaleSelector;
use App\Filament\Resources\Components\LocationSelector;
use App\Filament\Resources\Components\RegionsSelector;
use App\Filament\Resources\Components\RestrictedMarkdownEditor;
use App\Filament\Resources\OffersResource\Pages;
use App\Filament\Resources\OffersResource\RelationManagers;
use App\Models\Offers;
use App\Models\Regions;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OffersResource extends Resource
{
    protected static ?string $model = Offers::class;

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
                        Forms\Components\Textarea::make('summary')
                            ->columnSpan(4)
                            ->required(),
                        RestrictedMarkdownEditor::getRestrictedMarkdownEditor('description'),
                        Forms\Components\TextInput::make('discount')
                            ->numeric()
                            ->columnSpan(2),
                        Forms\Components\Select::make('store_type')
                            ->options([
                                'shop' => 'Shop',
                                'online' => 'Online',
                            ])
                            ->columnSpan(2)
                            ->live()
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('redeem_interval')
                            ->options([
                                '1' => 'daily',
                                '7' => 'weekly',
                                '30' => 'monthly',
                                '365' => 'yearly',
                            ])
                            ->columnSpan(2)
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('generic_voucher')
                            ->columnSpan(3),
                        /*Forms\Components\TextInput::make('individual_vouchers')
                            ->columnSpan(3),*/
                        Forms\Components\TextInput::make('url')
                            ->label(__('Website'))
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('views')
                            ->disabled()
                            ->hidden(),
                        Forms\Components\Toggle::make('is_regional')
                            ->columnSpan(1)
                            ->live()
                            ->default(true)
                            ->required(),
                        RegionsSelector::getRegionsSelector(2, 'Regions', fn(Get $get) => $get('is_regional') == false),
                        LocationSelector::getLocationSelector(),
                        Forms\Components\Fieldset::make('Adressen-Information')
                            ->relationship('address')
                            ->schema([
                                Forms\Components\TextInput::make('street')
                                    ->label('Straße'),
                                Forms\Components\TextInput::make('housenumber')
                                    ->label('Hausnummer'),
                                Forms\Components\TextInput::make('city')
                                    ->label('Stadt'),
                                Forms\Components\TextInput::make('zip')
                                    ->label('Postleitzahl'),
                            ])
                            ->hidden(fn (Get $get) => $get('store_type') !== 'store'),
                        ImageUploader::getImageUploader('image', 'offers', __('Image'), true),
                        LocaleSelector::getLocaleSelector(),
                        Forms\Components\Section::make()
                            ->columns(6)
                            ->schema([
                                Forms\Components\DatePicker::make('valid_from')
                                    ->columnSpan(2)
                                    ->required(),
                                Forms\Components\DatePicker::make('valid_until')
                                    ->columnSpan(2)
                                    ->required(),
                                DateSelectors::getPublishedAtDateSelector(),
                            ]),
                    ])
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
                Tables\Columns\TextColumn::make('summary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('region_id')
                    ->label('Region')
                    ->sortable()
                    ->formatStateUsing(fn($state) => Regions::find($state)->name),
                Tables\Columns\TextColumn::make('locale')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('published_at')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (Offers $offers) {
                        $dateTime = new \DateTime($offers->published_at);
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
            RelationManagers\VoucherRedemptionsRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffers::route('/create'),
            'edit' => Pages\EditOffers::route('/{record}/edit'),
        ];
    }
}
