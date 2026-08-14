<div>
    <h2 class="text-lg font-bold mb-2">Datos del Usuario</h2>
    <p><strong>Nombre:</strong> {{ $user->name }}</p>
    <p><strong>Correo:</strong> {{ $user->email }}</p>

    <h2 class="text-lg font-bold mt-4 mb-2">Perfil</h2>
    @if ($profile)
        <p><strong>Teléfono:</strong> {{ $profile->telefono }}</p>
        <p><strong>Calle:</strong> {{ $profile->calle }}</p>
        <p><strong>Número Exterior:</strong> {{ $profile->numero_exterior }}</p>
        <p><strong>Número Interior:</strong> {{ $profile->numero_interior }}</p>
        <p><strong>Colonia:</strong> {{ $profile->colonia }}</p>
        <p><strong>Entre Calles:</strong> {{ $profile->entre_calles }}</p>
        <p><strong>Código Postal:</strong> {{ $profile->codigo_postal }}</p>
        <p><strong>Ciudad:</strong> {{ $profile->ciudad }}</p>
        <p><strong>Estado:</strong> {{ $profile->estado }}</p>
        <p><strong>País:</strong> {{ $profile->pais }}</p>
    @else
        <p class="text-red-600">No hay información de perfil disponible.</p>
    @endif
</div>
