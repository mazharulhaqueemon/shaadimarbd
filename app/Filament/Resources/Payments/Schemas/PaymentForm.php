<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Admin selects the user
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),

                // Admin selects the plan user is requesting
                Select::make('plan_id')
                    ->relationship('plan', 'plan_name')
                    ->required()
                    ->searchable(),

                // Payment method
                TextInput::make('payment_method')
                    ->required()
                    ->maxLength(100),

                // Transaction ID
                TextInput::make('transaction_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                // Optional sender info
                TextInput::make('sender_name')
                    ->maxLength(100),

                TextInput::make('sender_phone')
                    ->tel()
                    ->maxLength(20),

                // Screenshot upload to S3
                FileUpload::make('screenshot_path')
                    ->label('Payment Screenshot')
                    ->image()
                    ->disk('s3')                  // store in S3
                    ->directory('payments')       // folder in bucket
                    ->visibility('public')        // publicly accessible
                    ->nullable()
                    ->enableDownload()            // allow admin to download
                    ->enableOpen()                // allow admin to preview
                    ->hint('Optional: Upload a screenshot of the payment.'),

                // Status select
                Select::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
