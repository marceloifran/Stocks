<div class='p-4 text-center'>
    <h2 class='text-lg font-bold mb-4'>Firma de {{ $nombre }}</h2>
    <div class='flex justify-center'>
        <img src='{{ $firma }}' class='max-w-full h-auto border rounded-lg shadow-lg' style='max-height: 300px;'>
    </div>
    <p class='mt-4 text-sm text-gray-600'>Fecha: {{ $fecha }}</p>
    <p class='mt-1 text-sm text-gray-600'>Equipo: {{ $equipo }}</p>
</div>
