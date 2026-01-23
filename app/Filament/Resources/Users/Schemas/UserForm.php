<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Plan;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),

                Toggle::make('is_admin')->required(),

                Select::make('account_created_by')
                    ->options(['self' => 'Self', 'referral' => 'Referral', 'system' => 'System'])
                    ->default('self')
                    ->required(),

                // Password field: masked and readonly
                TextInput::make('password')
                    ->label('Password')
                    ->type('password')           // ensures input is masked
                    ->default('********')        // show masked value
                    ->disabled()                 // cannot be edited
                    ->dehydrated(false)          // do not save
                    ->extraInputAttributes([
                        'style' => ' background-color: #1f2937;' // light gray text on dark bg
                    ]),


                TextInput::make('otp'),

                DateTimePicker::make('otp_expires_at'),

                Select::make('plan_id')
                    ->label('Plan')
                    ->options(Plan::all()->pluck('plan_name', 'id'))
                    ->required()
                    ->searchable(),
            ]);
    }
}
