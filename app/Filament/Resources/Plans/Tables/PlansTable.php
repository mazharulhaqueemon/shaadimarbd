<?php

// namespace App\Filament\Resources\Plans\Tables;

// use Filament\Actions\BulkActionGroup;
// use Filament\Actions\DeleteBulkAction;
// use Filament\Actions\EditAction;
// use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Table;

// class PlansTable
// {
//     public static function configure(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 TextColumn::make('plan_name')
//                     ->searchable(),
//                 TextColumn::make('created_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//                 TextColumn::make('updated_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//                 TextColumn::make('profile_picture_limit')
//                     ->numeric()
//                     ->sortable(),
//                 TextColumn::make('phone_request_limit')
//                     ->numeric()
//                     ->sortable(),
//                 TextColumn::make('chat_duration_days')
//                     ->numeric()
//                     ->sortable(),
//             ])
//             ->filters([
//                 //
//             ])
//             ->recordActions([
//                 EditAction::make(),
//             ])
//             ->toolbarActions([
//                 BulkActionGroup::make([
//                     DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }
// }

namespace App\Filament\Resources\Plans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Table;

class PlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan_name')
                    ->label('Name')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->sortable(),

                TextColumn::make('duration')
                    ->label('Duration')
                    ->sortable(),

                BadgeColumn::make('popular')
                    ->label('Popular')
                    ->getStateUsing(fn ($record) => $record->popular ? 'Yes' : 'No')
                    ->colors([
                        'success' => fn ($state) => $state === 'Yes',
                        'secondary' => fn ($state) => $state === 'No',
                    ]),

                TextColumn::make('button_text')
                    ->label('Button Text'),

                TagsColumn::make('features')
                    ->label('Features')
                    ->getStateUsing(fn ($record) => is_array($record->features) ? $record->features : [])
                    ->separator(','),

                TextColumn::make('profile_picture_limit')
                    ->label('Profile Picture Limit')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('phone_request_limit')
                    ->label('Phone Request Limit')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('chat_duration_days')
                    ->label('Chat Duration (Days)')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
