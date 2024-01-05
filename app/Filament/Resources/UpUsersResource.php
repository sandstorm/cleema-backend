<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UpUsersResource\Pages;
use App\Filament\Resources\UpUsersResource\RelationManagers;
use App\Models\UpUsers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UpUsersResource extends Resource
{
    protected static ?string $model = UpUsers::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('created_by_id')
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by_id')
                    ->numeric(),
                Forms\Components\TextInput::make('username')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('provider')
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255),
                Forms\Components\Toggle::make('confirmed'),
                Forms\Components\Toggle::make('blocked'),
                Forms\Components\TextInput::make('referral_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->maxLength(255),
                Forms\Components\Toggle::make('accepts_surveys'),
                Forms\Components\Toggle::make('is_supporter'),
                Forms\Components\TextInput::make('referral_count')
                    ->numeric(),
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
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provider')
                    ->searchable(),
                Tables\Columns\IconColumn::make('confirmed')
                    ->boolean(),
                Tables\Columns\IconColumn::make('blocked')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('referral_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\IconColumn::make('accepts_surveys')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_supporter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('referral_count')
                    ->numeric()
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
            'index' => Pages\ListUpUsers::route('/'),
            'create' => Pages\CreateUpUsers::route('/create'),
            'edit' => Pages\EditUpUsers::route('/{record}/edit'),
        ];
    }
}
