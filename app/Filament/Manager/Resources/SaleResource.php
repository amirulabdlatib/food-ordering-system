<?php

namespace App\Filament\Manager\Resources;

use Filament\Forms;
use App\Models\Sale;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Restaurant;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Manager\Resources\SaleResource\Pages;
use App\Filament\Manager\Resources\SaleResource\RelationManagers;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('restaurant_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total_sales')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sales')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
                SelectFilter::make('restaurant_id')
                    ->options(
                        Restaurant::where('manager_id',auth()->user()->id)
                            ->pluck('name','id')
                    )
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
{
    $userRestaurantIds = Restaurant::where('manager_id', auth()->id())->pluck('id');

    return parent::getEloquentQuery()
        ->whereHas('restaurant', function ($query) use ($userRestaurantIds) {
            $query->whereIn('id', $userRestaurantIds);
        });
}


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'view' => Pages\ViewSale::route('/{record}'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
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
}