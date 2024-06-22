<?php

namespace App\Filament\Customer\Resources;

use Filament\Forms;
use App\Models\Menu;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use App\Models\Restaurant;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Customer\Resources\OrderResource\Pages;
use App\Filament\Customer\Resources\OrderResource\RelationManagers;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Hidden::make('customer_id')
                ->default(auth()->user()->id)
                ->required(),
            Forms\Components\Select::make('restaurant_id')
                ->label('Restaurant Name')
                ->options(
                    function () {
                        return Restaurant::where('is_approved', true)
                            ->where('status', true)
                            ->pluck('name', 'id');
                    }
                )
                ->required()
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('menu_items', [])),
            Forms\Components\Repeater::make('menu_items')
                ->label('Menu Items')
                ->schema([
                    Forms\Components\Select::make('menu_id')
                        ->label('Menu')
                        ->options(
                            function (callable $get) {
                                $restaurantId = $get('../../restaurant_id');
                                return Menu::where('restaurant_id', $restaurantId)->pluck('name', 'id');
                            }
                        )
                        ->required()
                        ->reactive(),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required()
                        ->reactive(), 
                    Forms\Components\TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->disabled()
                        ->numeric(),
                ])
                ->dehydrated()
                ->defaultItems(0)
                ->createItemButtonLabel('Add Menu Item')
                ->columns(4)
                ->columnSpan(4)
                ->afterStateUpdated(function (callable $get, callable $set, $state) {
                    $menuItems = $get('menu_items');
                    $totalAmount = 0;
                    foreach ($menuItems as $index => $menuItem) {
                        $menu = Menu::find($menuItem['menu_id']);
                        $quantity = $menuItem['quantity'];
                        $subtotal = $menu ? $menu->price * $quantity : 0;
                        $set("menu_items.$index.subtotal", $subtotal);
                        $totalAmount += $subtotal;
                    }
                    $set('total_amount', round($totalAmount,2));
                }),
            Forms\Components\Select::make('order_type')
                ->options([
                    'delivery' => 'Delivery',
                    'pickup' => 'Pickup',
                ])
                ->required(),
            Forms\Components\Select::make('payment_method')
                ->options([
                    'stripe' => 'Stripe',
                ])
                ->required(),
            Forms\Components\Hidden::make('order_status')
                ->default('submitted')
                ->required(),
            Forms\Components\TextInput::make('total_amount')
                ->label('Total Amount')
                ->readOnly()
                ->required()
                ->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at','desc')
            ->filters([
                //
                SelectFilter::make('restaurant_id')
                    ->label('Restaurant Name')
                    ->options(
                        Restaurant::pluck('name','id')
                    ),
                SelectFilter::make('order_type')
                    ->options([
                        "delivery"=>"Delivery",
                        "pickup"=>"Pickup"
                    ]),
                SelectFilter::make('order_status')
                    ->options([
                        "submitted"=>"Submitted",
                        "paid"=>"Paid",
                        "accepted"=>"Accepted",
                        "rejected"=>"Rejected"
                    ])
            ],layout:FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Pay')
                    ->url(fn (Order $record): string => route('customer.orders.pay', $record))
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
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return abort(403);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('customer_id', auth()->id());
    }
}