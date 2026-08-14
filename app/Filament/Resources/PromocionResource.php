<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromocionResource\Pages;
use App\Filament\Resources\PromocionResource\RelationManagers;
use App\Models\Promocion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use App\Models\Producto;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Exports\PromocionesExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\BulkAction;

class PromocionResource extends Resource
{
    protected static ?string $model = Promocion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
        public static function getNavigationLabel(): string
    {
        return 'Promociones'; // Texto que aparecerá en el menú
    }

    public static function getTitle(): string
    {
        return 'Promociones'; // o el texto que desees
    }



    public static function getModelLabel(): string
    {
        return 'Promociones'; // Texto singular que aparecerá en botones, formularios, etc.
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('producto_id')
                    ->label('Producto')
                    ->options(function () {
                        return Producto::whereDoesntHave('promocion')->pluck('nombre_comercial', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->rules([
                        'required',
                        'unique:promociones,producto_id',
                    ]),

                TextInput::make('titulo')
                    ->required(),

                TextInput::make('descuento')
                    ->numeric()
                    ->nullable(),

                DatePicker::make('inicio')
                    ->required(),

                DatePicker::make('fin')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('producto.nombre_comercial')->label('Producto'),

                TextColumn::make('descuento'),
                TextColumn::make('inicio')->date(),
                TextColumn::make('fin')->date(),
            ])
            ->defaultSort('inicio', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                BulkAction::make('exportar_promociones')
    ->label('Exportar Promociones')
    ->requiresConfirmation()
    ->action(function ($records) {
        return Excel::download(
            new PromocionesExport($records),
            'promociones_exportadas.xlsx'
        );
    }),
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
            'index' => Pages\ListPromocions::route('/'),
            'create' => Pages\CreatePromocion::route('/create'),
            'edit' => Pages\EditPromocion::route('/{record}/edit'),
        ];
    }
}
