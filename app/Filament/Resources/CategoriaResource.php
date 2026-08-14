<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaResource\Pages;
use App\Filament\Resources\CategoriaResource\RelationManagers;
use App\Models\Categoria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\RichEditor;
use App\Exports\CategoriasExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\BulkAction;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationLabel(): string
    {
        return 'Catálogo'; // Texto que aparecerá en el menú
    }

    public static function getTitle(): string
    {
        return 'Catálogo'; // o el texto que desees
    }



    public static function getModelLabel(): string
    {
        return 'Catálogo'; // Texto singular que aparecerá en botones, formularios, etc.
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->label('Catálogo nombre'),
                RichEditor::make('descripcion')
                    ->label('Descripción')

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('nombre')->searchable()->label('Nombre'),
                TextColumn::make('created_at')->label('Created')->dateTime(),
            ])
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

BulkAction::make('exportar_categorias')
    ->label('Exportar Categorías')
    ->requiresConfirmation()
    ->action(function ($records) {
        return Excel::download(
            new CategoriasExport($records),
            'catalogos_exportadas.xlsx'
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
            'index' => Pages\ListCategorias::route('/'),
            'create' => Pages\CreateCategoria::route('/create'),
            'edit' => Pages\EditCategoria::route('/{record}/edit'),
        ];
    }
}
