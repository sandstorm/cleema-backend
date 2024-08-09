<?php

namespace App\Filament\Resources\Components;

use Filament\Forms\Components\MarkdownEditor;

class RestrictedMarkdownEditor
{
    public static function getRestrictedMarkdownEditor(String $fieldName, String $label = null): MarkdownEditor
    {
        return MarkdownEditor::make($fieldName)
            ->required()
            ->disableToolbarButtons([
                'attachFiles',
                'italic',
                'strike',
                'table',
            ])
            ->label($label)
            ->columnSpanFull()
            ->live();
    }
}
