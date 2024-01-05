<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizzesResource\Pages;
use App\Filament\Resources\QuizzesResource\RelationManagers;
use App\Models\Quizzes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuizzesResource extends Resource
{
    protected static ?string $model = Quizzes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('created_by_id')
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by_id')
                    ->numeric(),
                Forms\Components\Textarea::make('question')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('correct_answer')
                    ->maxLength(255),
                Forms\Components\Textarea::make('explanation')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('published_at'),
                Forms\Components\TextInput::make('locale')
                    ->maxLength(255),
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_by_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('correct_answer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('locale')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
            ])
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
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuizzes::route('/create'),
            'edit' => Pages\EditQuizzes::route('/{record}/edit'),
        ];
    }
}
