<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Columns\BadgeColumn;

use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkAction;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getTitle(): string
    {
        return 'Usuarios'; // o el texto que desees
    }

    public static function getNavigationLabel(): string
    {
        return 'Usuarios'; // Texto que aparecerá en el menú
    }

    public static function getModelLabel(): string
    {
        return 'Usuarios'; // Texto singular que aparecerá en botones, formularios, etc.
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required(),

                // TextInput::make('password')
                // ->label('Contraseña')
                // ->password()
                // ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                // ->dehydrateStateUsing(fn ($state) => !empty($state) ? bcrypt($state) : null)
                // ->hiddenOn('edit')
                // ->maxLength(255),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)  // Solo requerido en la creación
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)  // Encriptar la contraseña solo si no está vacía
                    ->hiddenOn('edit')  // Esconde el campo en la página de edición
                    ->maxLength(255),

                // Agregar un campo de contraseña en el formulario de edición para que el usuario pueda cambiarla
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->nullable()
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state)) // <- ESTA LÍNEA ES CLAVE
                    ->maxLength(255)
                    ->hiddenOn('create'),
                Select::make('role')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Administrador',
                        'cliente' => 'Cliente',
                    ])
                    ->required(),

                // DatePicker::make('happy')
                //     ->label('Fecha de Nacimiento'),

                // TextInput::make('telefono')
                //     ->label('Teléfono')
                //     ->tel()
                //     ->maxLength(20),

                Toggle::make('activo')
                    ->label('Activo')
                    ->visible(fn($state, $record) => $record !== null) //
                    ->default(true),


                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->sortable()->searchable(),
                TextColumn::make('email')->label('Correo Electrónico')->sortable()->searchable(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                BadgeColumn::make('role')
                    ->label('Rol')
                    ->colors([
                        'admin' => 'danger',
                        'cliente' => 'success',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make('verPerfil')
                    ->label('Ver Perfil')
                    ->modalHeading('Perfil del Usuario')
                    ->modalWidth('lg')
                    ->modalContent(function ($record) {
                        $profile = $record->profile; // Relación desde el modelo User

                        return view('filament.resources.users.view-profile', [
                            'user' => $record,
                            'profile' => $profile,
                        ]);
                    }),
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
                        // Filtramos solo los campos deseados para cada usuario
                        $data = $records->map(fn($record) => [
                            $record->id,
                            $record->name,
                            $record->email,
                            $record->role,
                            $record->activo ? 'Sí' : 'No',
                        ]);

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\UsersExport($data),
                            'usuarios_seleccionados.xlsx'
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
