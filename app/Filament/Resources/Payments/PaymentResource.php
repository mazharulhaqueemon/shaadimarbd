<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages\CreatePayment;
use App\Filament\Resources\Payments\Pages\EditPayment;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $recordTitleAttribute = 'transaction_id';

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'edit'   => EditPayment::route('/{record}/edit'),
        ];
    }

    
    // Customize the default query to always eager load relations
     
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user.plan', 'plan']);
    }
}
