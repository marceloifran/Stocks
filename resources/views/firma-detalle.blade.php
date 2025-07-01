<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma de {{ $stockMovement->personal->nombre }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8 px-4">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-4">
                <h1 class="text-white text-xl font-bold">Firma de EPP</h1>
            </div>

            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $stockMovement->personal->nombre }}</h2>
                    <p class="text-gray-600">{{ $stockMovement->personal->departamento ?? 'Sin departamento' }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Equipo</p>
                        <p class="font-medium">{{ $stockMovement->stock->nombre }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Fecha</p>
                        <p class="font-medium">{{ $stockMovement->fecha_movimiento->format('d/m/Y') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Tipo</p>
                        <p class="font-medium">{{ $stockMovement->tipo ?? 'No especificado' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Certificación</p>
                        <p class="font-medium">{{ $stockMovement->certificacion ?? 'No especificado' }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold mb-4">Firma</h3>
                    <div class="flex justify-center">
                        <img src="{{ $stockMovement->firma }}" class="max-w-full border rounded-lg shadow-lg"
                            style="max-height: 300px;">
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Firma registrada el {{ $stockMovement->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 text-center">
                <button onclick="window.print()"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">
                    Imprimir
                </button>
                <button onclick="window.close()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</body>

</html>
