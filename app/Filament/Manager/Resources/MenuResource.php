<?php

namespace App\Filament\Manager\Resources;

use Filament\Forms;
use App\Models\Menu;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Restaurant;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Manager\Resources\MenuResource\Pages;
use Illuminate\Database\Query\Builder as QueryBuilder;
use App\Filament\Manager\Resources\MenuResource\RelationManagers;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-4';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('restaurant.name')
                    ->label('Restaurant Name')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('name')
                    ->label('Item')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->label('Price (RM)')
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Item')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('myr',2)
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'view' => Pages\ViewMenu::route('/{record}'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $userRestaurantIds = Restaurant::where('manager_id', auth()->id())->pluck('id');
        return parent::getEloquentQuery()->whereIn('restaurant_id', $userRestaurantIds);
    }

}