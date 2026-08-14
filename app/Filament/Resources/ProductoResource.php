<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

use Filament\Forms\Set;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Columns;
use App\Exports\ProductosExport;
use Filament\Tables\Actions\BulkAction;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                TextInput::make('nombre_comercial')
                    ->label('Nombre Comercial')
                    ->required()
                    ->reactive()
                    ->debounce(1000)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->label('Slug')
                    ->disabled()
                    ->required(),
                Select::make('categoria_id')
                    ->relationship('categoria', 'nombre')->label('Catalogo')->required(),
                Select::make('marca_id')
                    ->relationship('marca', 'nombre')->label('Marcas')->required(),
                TextInput::make('modelo')
                    ->label('Modelo'),
                TextInput::make('item')
                    ->label('Item'),
                Toggle::make('destacado')
                    ->label('Destacado')
                    ->default(false),
                RichEditor::make('descripcion')
                    ->label('Descripción')
                    ->fileAttachmentsDisk('public')        // El disco de archivos configurado en config/filesystems.php
                    ->fileAttachmentsDirectory('attachments') // Carpeta dentro del disco donde se guardan las imágenes
                    ->fileAttachmentsVisibility('public')  // Opcional, para hacerlas accesibles públicamente
                    ->columnSpanFull(),
                TextInput::make('precio')->label('Precio')->numeric(),
                TextInput::make('medidas')
                    ->label('Medidas'),

                TextInput::make('peso')
                    ->label('Peso'),

                // Select::make('industria')->label('Industria')->options([
                //     'restaurantes' => 'Restaurantes',
                //     'pizzeria' => 'Pizzeria',
                //     'cafetería' => 'Cafeteria',
                //     'carniceria' => 'Carniceria',
                //     'panadería' => 'Panaderia',
                //     'pastelería' => 'Pasteleria',
                //     'comedores industrial' => 'Comedores Industrial',
                //     'bares / centro nocturnos' => 'Bares / Centro Nocturnos',
                //     'hoteles' => 'Hoteles'
                // ]),
                CheckboxList::make('industria')
                    ->label('Industrias')
                    ->options([
                        'restaurantes' => 'Restaurantes',
                        'pizzeria' => 'Pizzería',
                        'cafetería' => 'Cafetería',
                        'carniceria' => 'Carnicería',
                        'panadería' => 'Panadería',
                        'pastelería' => 'Pastelería',
                        'comedores industrial' => 'Comedores Industrial',
                        'bares / centro nocturnos' => 'Bares / Centro Nocturnos',
                        'hoteles' => 'Hoteles',
                    ])
                    ->columns(2),

                Grid::make(2) // divide en 2 columnas
                    ->schema([
                        Select::make('tipo')
                            ->label('Tipo')
                            ->options([
                                'equipos' => 'Equipos',
                                'refaccione' => 'Refaccione',
                                'accesorios' => 'Accesorios',
                                'consumibles' => 'Consumibles',
                            ])
                            ->columnSpan(1), // ocupa solo la mitad de la fila
                    ]),





                FileUpload::make('imagen_slug')
                    ->directory('slug')
                    ->image()
                    ->label('Imagen Slug'),

                FileUpload::make('galeria_imagenes')
                    ->label('Galeria')
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->directory('galeria')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])

                    ->maxFiles(22),
                FileUpload::make('ficha')
                    ->label('Ficha Pdf')
                    ->reorderable()
                    ->imagePreviewHeight('150')
                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                    ->directory('ficha'),
                FileUpload::make('manual')
                    ->label('Manual Pdf')
                    ->reorderable()
                    ->imagePreviewHeight('150')
                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                    ->directory('manual'),
                Toggle::make('activo')
                    ->label('Producto activo')
                    ->visible(fn($state, $record) => $record !== null) //
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nombre_comercial')->searchable(),
                ImageColumn::make('imagen_slug')
                    ->label('Logo')
                    ->size(20)
                    ->url(fn($record) => Storage::url($record->imagen_slug))
                    ->circular()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->alignCenter()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

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
                BulkAction::make('export_excel')
                    ->label('Exportar a Excel')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $data = $records->map(function ($record) {
                            return [
                                'ID'                => $record->id,
                                'Nombre Comercial' => $record->nombre_comercial,
                                'Slug'             => $record->slug,
                                'Modelo'           => $record->modelo,
                                'Item'             => $record->item,
                                'Categoría' => $record->categoria?->nombre ?? '—',
                                'Marca'     => $record->marca?->nombre ?? '—',
                                'Destacado'        => $record->destacado ? 'Sí' : 'No',
                                'Descripción'      => is_array($record->descripcion) ? json_encode($record->descripcion) : $record->descripcion,
                                'Precio'           => $record->precio,
                                'Galería Imágenes' => is_array($record->galeria_imagenes) ? implode(' | ', $record->galeria_imagenes) : $record->galeria_imagenes,
                                'Imagen Slug'      => $record->imagen_slug,
                                'Medidas'          => $record->medidas,
                                'Peso'             => $record->peso,
                                'Industria'        => $record->industria,
                                'Tipo'             => $record->tipo,
                                'Ficha Técnica'    => $record->ficha,
                                'Manual'           => $record->manual,
                                'Activo'           => $record->activo ? 'Sí' : 'No',
                                'Orden'            => $record->orden,
                            ];
                        })->toArray();

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new ProductosExport($data),
                            'productos_seleccionados.xlsx'
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
