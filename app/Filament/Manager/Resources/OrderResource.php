<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\OrderResource\Pages;
use App\Filament\Manager\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_id')
                    ->numeric()
                    ->readOnly(),
                Forms\Components\TextInput::make('restaurant_id')
                    ->required()
                    ->numeric()
                    ->readOnly(),
                Forms\Components\TextInput::make('order_type')
                    ->required()
                    ->readOnly(),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->readOnly(),
                Forms\Components\TextInput::make('payment_method')
                    ->required()
                    ->maxLength(255)
                    ->readOnly(),
                Forms\Components\Select::make('order_status')
                    ->options([
                    "accepted"=>"Accepted",
                    "rejected"=>"Rejected"
                ])
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_type'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_status')
                    ->badge()
                    ->color(function ($state){
                        if ($state === 'accepted') {
                            return 'success';
                        } elseif ($state === 'submitted') {
                            return 'info';
                        }
                        return 'danger';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
                SelectFilter::make('order_status')
                    ->options([
                        "submitted"=>"Submitted",
                        "accepted"=>"Accepted",
                        "rejected"=>"Rejected"
                    ]),
                SelectFilter::make('order_type')
                    ->options([
                        "delivery"=>"Delivery",
                        "pickup"=>"Pickup",
                       
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return abort(403);
    }

    public static function canCreate(): bool
    {
        return abort(403);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('restaurant', function ($query) {
                $query->where('manager_id', auth()->user()->id); // can auth()->id()
            });
    }
}