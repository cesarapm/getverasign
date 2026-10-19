<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\WebhookController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\CustomerOrderAccessController;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SignatureDocumentController;
use App\Http\Controllers\DashboardController;
use App\Models\Order;







Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::get('/user', [AuthController::class, 'getProfile']);
    
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/signature-documents', [SignatureDocumentController::class, 'store']);
    Route::get('/signature-documents/{id}', [SignatureDocumentController::class, 'show']);
    Route::get('/signature-documents/{id}/download-signed', [SignatureDocumentController::class, 'downloadSigned']);
    Route::delete('/signature-documents/{id}', [SignatureDocumentController::class, 'destroy']);
});

// Rutas de productos
Route::get('/products', [ProductoController::class, 'index']);
Route::get('/productos', [ProductoController::class, 'index']); // Compatibilidad
Route::get('/products/{id}', [ProductoController::class, 'show']);
Route::get('/productos/destacado', [ProductoController::class, 'destacado']);
Route::get('/productos/{slug}', [ProductoController::class, 'buscarPorSlug']);
Route::get('/buscar-productos', [ProductoController::class, 'buscar']);
Route::get('/buscar-texto', [ProductoController::class, 'buscarPorTexto']);

// Rutas de categorías
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{id}', [CategoriaController::class, 'show']);

// Rutas de marcas
Route::get('/marcas', [MarcaController::class, 'index']);
Route::get('/marcas/{id}', [MarcaController::class, 'show']);


Route::post('/clientes/pedidos/acceso', [CustomerOrderAccessController::class, 'requestCode']);
Route::post('/clientes/pedidos/verificar', [CustomerOrderAccessController::class, 'verifyCode']);

Route::get('/payment-methods', [PaymentMethodsController::class, 'index']);
Route::get('/payment-methods/bank-info', [PaymentMethodsController::class, 'getBankInfo']);

Route::post('/orders', [OrderController::class, 'store']);



Route::post('/crear-pedido', [PedidoController::class, 'crearPedido']);
Route::post('/crear-pedido/transferencia', [PedidoController::class, 'crearPedidoTransferencia']);
Route::post('/crear-pedido/paypal', [PedidoController::class, 'crearPedidoPayPal']);
Route::post('/paypal/capturar-pago', [PedidoController::class, 'capturarPagoPayPal']);
Route::get('/pedidos/seguimiento', [OrderTrackingController::class, 'show']);
Route::get('/ordenes-recientes', [PedidoController::class, 'ordenesRecientes']);
Route::post('/clientes/pedidos/acceso', [CustomerOrderAccessController::class, 'requestCode']);
Route::post('/clientes/pedidos/verificar', [CustomerOrderAccessController::class, 'verifyCode']);

Route::delete('/ordenes/{id}', [PedidoController::class, 'ordenescancelar']);

// ⚠️ Webhook de Mercado Pago - DEBE estar fuera del middleware de autenticación
Route::post('/mercado-pago/webhook', [WebhookController::class, 'handleWebhook']);

// ⚠️ Webhook de PayPal - DEBE estar fuera del middleware de autenticación
Route::post('/paypal/webhook', [WebhookController::class, 'handlePayPalWebhook']);

// Ruta pública para cancelar orden cuando el pago falla
Route::post('/cancelar-orden-pago-fallido', [PedidoController::class, 'cancelarOrdenPorPagoFallido']);