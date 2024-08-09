<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminUsersResource\Pages;
use App\Filament\Resources\AdminUsersResource\RelationManagers;
use App\Models\AdminRoles;
use App\Models\AdminUsers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class AdminUsersResource extends Resource
{
    protected static ?string $model = AdminUsers::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('firstname')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('lastname')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('username')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\TextInput::make('prefered_language')
                    ->maxLength(255)
                    ->hidden()
                    ->disabled(),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Role')
                    ->preload()
                    ->required()
                    ->searchable()
                    ->live(),
                Forms\Components\Toggle::make('is_active'),
                Forms\Components\Toggle::make('blocked'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('firstname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('blocked')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUsers::route('/create'),
            'edit' => Pages\EditAdminUsers::route('/{record}/edit'),
        ];
    }
}
