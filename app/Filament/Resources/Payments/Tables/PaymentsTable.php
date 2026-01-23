<?php
namespace App\Filament\Resources\Payments\Tables;
use Illuminate\Support\Facades\Storage;

use App\Models\Payment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.plan.plan_name')
                    ->label('Current Plan')
                    ->sortable(),

                TextColumn::make('plan.plan_name')
                    ->label('Requested Plan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('transaction_id')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->searchable(),

                TextColumn::make('sender_name')
                    ->searchable(),

                TextColumn::make('sender_phone')
                    ->searchable(),

                ImageColumn::make('screenshot_path')
                    ->label('Screenshot')
                    ->disk('s3')
                    ->url(fn(Payment $record) => $record->screenshot_path ? Storage::disk('s3')->url($record->screenshot_path) : null)
                    ->height(50)
                    ->width(50)
                    ->rounded()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge(fn(Payment $record) => match($record->status) {
                        Payment::STATUS_PENDING  => 'warning',
                        Payment::STATUS_APPROVED => 'success',
                        Payment::STATUS_REJECTED => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
