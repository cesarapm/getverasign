<!-- resources/views/filament/resources/pagos/productos.blade.php -->
<div class="space-y-4">
    <h3 class="text-lg font-bold">🧾 Productos en la Orden</h3>

    @if($productos->isEmpty())
        <p>No hay productos asociados a esta orden.</p>
    @else
        <table class="table-auto w-full text-left border border-gray-300">
            <thead>
                <tr class="">
                    <th class="px-4 py-2 border">Nombre</th>
                    <th class="px-4 py-2 border">Cantidad</th>
                    <th class="px-4 py-2 border">Precio Unitario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                    <tr>
                        <td class="px-4 py-2 border">{{ $producto->nombre }}</td>
                        <td class="px-4 py-2 border">{{ $producto->cantidad }}</td>
                        <td class="px-4 py-2 border">${{ number_format($producto->precio_unitario, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
