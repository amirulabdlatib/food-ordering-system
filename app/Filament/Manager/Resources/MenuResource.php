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
use Filament\Forms\Components\Grid;
use App\Filament\Manager\Resources\MenuResource\RelationManagers;
use Filament\Tables\Filters\SelectFilter;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-4';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                    Forms\Components\Select::make('restaurant_id')
                        ->label('Restaurant Name')
                        ->required()
                        ->options(
                            function () {
                                return Restaurant::where('manager_id', auth()->id())
                                    ->where('is_approved',true)
                                    ->where('status',true)
                                    ->pluck('name', 'id');
                            }
                        ),
                    Forms\Components\TextInput::make('name')
                        ->label('Item')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('price')
                        ->label('Price (RM)')
                        ->required()
                        ->numeric(),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
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
                    Restaurant::where('manager_id',auth()->id())
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