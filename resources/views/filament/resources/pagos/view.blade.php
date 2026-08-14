<div class="space-y-6">

    {{-- Detalles del Pago --}}
    <div class="border p-4 rounded-md  shadow-sm ">
        <h3 class="text-lg font-bold mb-2">💳 Detalles del Pago</h3>

        @if ($pago)
            <p><strong>ID de Pago:</strong> {{ $pago->id_pago }}</p>
            <p><strong>Descripción:</strong> {{ $pago->descripcion }}</p>
            <p><strong>Monto Transacción:</strong> ${{ number_format($pago->monto_transaccion, 2) }}</p>
            <p><strong>Monto Recibido Neto:</strong> ${{ number_format($pago->monto_recibido_neto, 2) }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($pago->estado) }}</p>
            <p><strong>Fecha de Aprobación:</strong> {{ $pago->fecha_aprobacion }}</p>
            <p><strong>Fecha de Creación:</strong> {{ $pago->fecha_creacion }}</p>
            <p><strong>Última Actualización:</strong> {{ $pago->fecha_ultima_actualizacion }}</p>
            <p><strong>Método de Pago:</strong> {{ $pago->metodo_pago }}</p>
        @else
            <p class="text-red-500">❌ No se encontró un pago asociado.</p>
        @endif
    </div>

    {{-- Detalles del Pedido --}}
    <div class="border p-4 rounded-md  shadow-sm">
        <h3 class="text-lg font-bold mb-2">🧾 Detalles del Pedido</h3>
        <p><strong>Tipo de Compra:</strong> {{ $record->metodo_pago ?? '-' }}</p>
        <p><strong>Usuario:</strong> {{ $record->user->name ?? '-' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($record->status) }}</p>
        <p><strong>Creado el:</strong> {{ $record->created_at->format('d-m-Y H:i') }}</p>
    </div>

    {{-- Detalles de Envío --}}
    @if (!empty($record->envio))
        <div class="border p-4 rounded-md  shadow-sm ">
            <h3 class="text-lg font-bold mb-2">🚚 Detalles del Envío</h3>
            <p><strong>Nombre:</strong> {{ $record->envio['nombre'] ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $record->envio['email'] ?? '-' }}</p>
            <p><strong>Teléfono:</strong> {{ $record->envio['telefono'] ?? '-' }}</p>
            <p><strong>Calle:</strong> {{ $record->envio['calle'] ?? '-' }}</p>
            <p><strong>Número Exterior:</strong> {{ $record->envio['numero_exterior'] ?? '-' }}</p>
            <p><strong>Número Interior:</strong> {{ $record->envio['numero_interior'] ?? '-' }}</p>
            <p><strong>Colonia:</strong> {{ $record->envio['colonia'] ?? '-' }}</p>
            <p><strong>Entre Calles:</strong> {{ $record->envio['entre_calles'] ?? '-' }}</p>
            <p><strong>Código Postal:</strong> {{ $record->envio['codigo_postal'] ?? '-' }}</p>
            <p><strong>Ciudad:</strong> {{ $record->envio['ciudad'] ?? '-' }}</p>
            <p><strong>Estado:</strong> {{ $record->envio['estado'] ?? '-' }}</p>
        </div>
    @endif

</div>
