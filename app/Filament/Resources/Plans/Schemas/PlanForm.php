<?php

// namespace App\Filament\Resources\Plans\Schemas;

// use Filament\Forms\Components\TextInput;
// use Filament\Forms\Components\Textarea;
// use Filament\Schemas\Schema;

// class PlanForm
// {
//     public static function configure(Schema $schema): Schema
//     {
//         return $schema
//             ->components([
//                 TextInput::make('plan_name')
//                     ->required(),
//                 Textarea::make('description')
//                     ->columnSpanFull(),
//                 TextInput::make('profile_picture_limit')
//                     ->required()
//                     ->numeric()
//                     ->default(0),
//                 TextInput::make('phone_request_limit')
//                     ->tel()
//                     ->required()
//                     ->numeric()
//                     ->default(0),
//                 TextInput::make('chat_duration_days')
//                     ->required()
//                     ->numeric()
//                     ->default(0),
//             ]);
//     }
// }


namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('plan_name')
                    ->required()
                    ->label('Plan Name'),

                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->required()
                    ->label('Price')
                    ->placeholder('e.g., Free, $19'),

                TextInput::make('duration')
                    ->required()
                    ->label('Duration')
                    ->placeholder('e.g., Forever, per month'),

                Checkbox::make('popular')
                    ->label('Popular Plan'),

                TextInput::make('button_text')
                    ->label('Button Text')
                    ->required()
                    ->placeholder('e.g., Get Started'),

                Repeater::make('features')
                    ->label('Features')
                    ->schema([
                        TextInput::make('feature')
                            ->required()
                            ->placeholder('Enter feature'),
                    ])
                    ->columnSpanFull()
                    ->createItemButtonLabel('Add Feature')
                    // Flatten array of objects into array of strings when saving
                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? array_column($state, 'feature') : [])
                    // Initialize state from DB (convert array of strings to array of objects)
                    ->afterStateHydrated(fn ($component, $state) => $component->state(array_map(fn($f) => ['feature' => $f], $state ?? []))),

                
                TextInput::make('profile_picture_limit')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Profile Picture Limit'),

                TextInput::make('phone_request_limit')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Phone Request Limit'),

                TextInput::make('chat_duration_days')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Chat Duration (Days)'),
            ]);
    }
}
