<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsTagsResource\Pages;#
use App\Models\NewsTags;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NewsTagsResource extends Resource
{
    protected static ?string $model = NewsTags::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                Tables\Columns\TextInputColumn::make('value')
                    ->label('Name')
                    ->rules(['required', 'max:255'])
                    ->searchable()
                    ->extraAttributes(['style' => 'width: 80%'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('uses')
                    ->sortable()
                    ->getStateUsing(fn(NewsTags $record) => $record->newsEntries()->count())
            ])
            ->filters([
                //
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsTags::route('/'),
        ];
    }
}
