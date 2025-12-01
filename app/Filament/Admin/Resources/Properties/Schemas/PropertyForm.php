<?php

namespace App\Filament\Admin\Resources\Properties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('state')
                    ->required(),
                TextInput::make('price_per_night')
                    ->required()
                    ->numeric(),
                TextInput::make('bedrooms')
                    ->required()
                    ->numeric(),
                TextInput::make('bathrooms')
                    ->required()
                    ->numeric(),
                TextInput::make('max_guests')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
