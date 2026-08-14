<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    

       User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Cesar Mata',
                'password' => Hash::make('123123'),
            ]
        );


        // Ejecutar seeders
        // $this->call([
        //     CategoriaSeeder::class,
        //     MarcaSeeder::class,
        //     ProductoSeeder::class,
        // ]);
         $this->configurePaymentMethods();
    }
    /**
     * Configurar métodos de pago por defecto
     */
    private function configurePaymentMethods(): void
    {
        // Habilitar MercadoPago por defecto
        Setting::updateOrCreate(
            ['key' => 'mercadopago_enabled'],
            ['value' => '1', 'description' => 'Habilitar Mercado Pago']
        );

        // PayPal deshabilitado por defecto (requiere configuración)
        Setting::updateOrCreate(
            ['key' => 'paypal_enabled'],
            ['value' => '0', 'description' => 'Habilitar PayPal']
        );

        Setting::updateOrCreate(
            ['key' => 'paypal_client_id'],
            ['value' => '', 'description' => 'Client ID de PayPal']
        );

        Setting::updateOrCreate(
            ['key' => 'paypal_client_secret'],
            ['value' => '', 'description' => 'Client Secret de PayPal']
        );

        Setting::updateOrCreate(
            ['key' => 'paypal_mode'],
            ['value' => 'sandbox', 'description' => 'Modo de PayPal (sandbox/live)']
        );

        // Habilitar Transferencia por defecto
        Setting::updateOrCreate(
            ['key' => 'transferencia_enabled'],
            ['value' => '1', 'description' => 'Habilitar Transferencia Bancaria']
        );

        // Datos bancarios de ejemplo
        Setting::updateOrCreate(
            ['key' => 'banco_nombre'],
            ['value' => 'Banco Ejemplo', 'description' => 'Nombre del banco']
        );

        Setting::updateOrCreate(
            ['key' => 'banco_beneficiario'],
            ['value' => 'Mi Negocio S.A. de C.V.', 'description' => 'Beneficiario']
        );

        Setting::updateOrCreate(
            ['key' => 'banco_cuenta'],
            ['value' => '1234567890', 'description' => 'Número de cuenta']
        );

        Setting::updateOrCreate(
            ['key' => 'banco_clabe'],
            ['value' => '012345678901234567', 'description' => 'CLABE interbancaria']
        );

        Setting::updateOrCreate(
            ['key' => 'whatsapp_transferencia'],
            ['value' => '5551234567', 'description' => 'WhatsApp para transferencias']
        );

        Setting::updateOrCreate(
            ['key' => 'transferencia_instrucciones'],
            ['value' => 'Por favor envía tu comprobante de pago por WhatsApp con tu número de orden.', 'description' => 'Instrucciones de transferencia']
        );
    }
}
