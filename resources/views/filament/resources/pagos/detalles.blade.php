<!-- resources/views/filament/resources/pagos/detalles.blade.php -->

@php
    $tabs = ['pago' => 'Pago', 'envio' => 'Envío', 'productos' => 'Productos'];
    $activeTab = request('tab', 'pago');
@endphp

<div x-data="{ tab: 'pago' }">
    <div class="flex space-x-4 border-b mb-4">
        @foreach ($tabs as $key => $label)
            <button
                x-on:click="tab = '{{ $key }}'"
                class="px-4 py-2 text-sm font-medium"
                :class="tab === '{{ $key }}' ? 'border-b-2 border-primary-600 text-primary-600' : 'text-gray-500'"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- TAB: Pago -->
    <div x-show="tab === 'pago'" class="space-y-2">
        <h3 class="font-bold">💳 Detalles del Pago</h3>
        @if($pago)
            <p><strong>ID:</strong> {{ $pago->id_pago }}</p>
            <p><strong>Descripción:</strong> {{ $pago->descripcion }}</p>
            <p><strong>Monto:</strong> ${{ number_format($pago->monto_transaccion, 2) }}</p>
            <p><strong>Estado:</strong> {{ $pago->estado }}</p>
            <p><strong>Fecha:</strong> {{ $pago->fecha_aprobacion }}</p>
        @else
            <p>No hay información de pago disponible.</p>
        @endif
    </div>

    <!-- TAB: Envío -->
    <div x-show="tab === 'envio'" class="space-y-2">
        <h3 class="font-bold">📦 Detalles de Envío</h3>
        @if($envio)
            <p><strong>Nombre:</strong> {{ $envio['nombre'] }}</p>
            <p><strong>Email:</strong> {{ $envio['email'] }}</p>
            <p><strong>Teléfono:</strong> {{ $envio['telefono'] }}</p>
            <p><strong>Dirección:</strong> {{ $envio['calle'] }} #{{ $envio['numero_exterior'] }} {{ $envio['colonia'] }}</p>
            <p><strong>Ciudad:</strong> {{ $envio['ciudad'] }} ({{ $envio['estado'] }})</p>
        @else
            <p>No hay datos de envío.</p>
        @endif
    </div>

    <!-- TAB: Productos -->
    <div x-show="tab === 'productos'" class="space-y-2">
        <h3 class="font-bold">🛒 Productos</h3>
        @if($productos->isEmpty())
            <p>No hay productos en esta orden.</p>
        @else
            <table class="w-full text-sm border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-1 text-left">Producto</th>
                        <th class="px-3 py-1 text-left">Cantidad</th>
                        <th class="px-3 py-1 text-left">Precio Unitario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $prod)
                        <tr>
                            <td class="px-3 py-1">{{ $prod->nombre }}</td>
                            <td class="px-3 py-1">{{ $prod->cantidad }}</td>
                            <td class="px-3 py-1">${{ number_format($prod->precio_unitario, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
