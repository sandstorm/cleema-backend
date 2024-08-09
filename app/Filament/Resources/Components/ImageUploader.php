<?php

namespace App\Filament\Resources\Components;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms;

class ImageUploader
{
    public static function getImageUploader(string $relation, string $uploadPath, string $label = '', bool|\Closure $isImageRequired = false): Forms\Components\Component {
        return Forms\Components\Fieldset::make($label)
            ->relationship($relation)
            ->schema([
                Forms\Components\FileUpload::make('url')
                    ->columnSpanFull()
                    ->directory('/uploads/' . $uploadPath)
                    ->disk('public')
                    ->acceptedFileTypes([
                        'image/png', 'image/gif', 'image/bmp', 'image/jpg', 'image/jpeg', 'image/webp',
                        // Frontend (at least Android) cannot show SVG, so don't include!
                    ])
                    ->imageEditor()
                    ->openable()
                    ->downloadable()
                    ->label('Image')
                    ->storeFileNamesIn('name')
                    ->visibility('public')
                    ->afterStateUpdated(function (Set $set, TemporaryUploadedFile $state) {
                        $fileNamePath = $state->getPath() . '/' . $state->getFilename();

                        $set('hash', $state->hashName());
                        $image = getimagesize($fileNamePath);
                        if (!empty($image)) {
                            $set('width', $image[0]);
                            $set('height', $image[1]);
                            $set('mime', $image['mime']);
                            $set('size', (float)$state->getSize() / 1000);
                            $set('ext', '.' . pathinfo($fileNamePath)['extension']);
                        }
                    })
                    ->required($isImageRequired),
                Forms\Components\Textarea::make('caption')
                    ->disabled(fn(Get $get): bool => !filled($get('url')))
                    ->maxLength(255)
                    ->label('Caption')
                    ->rows(2.5),
                Forms\Components\Textarea::make('alternative_text')
                    ->disabled(fn(Get $get): bool => !filled($get('url')))
                    ->label('Alternative Text')
                    ->rows(2.5),
                Forms\Components\Hidden::make('hash'),
                Forms\Components\Hidden::make('width'),
                Forms\Components\Hidden::make('height'),
                Forms\Components\Hidden::make('mime'),
                Forms\Components\Hidden::make('size'),
                Forms\Components\Hidden::make('ext'),
                Forms\Components\Hidden::make('folder_path')
                    ->default('/' . $uploadPath),
            ]);
    }
}
