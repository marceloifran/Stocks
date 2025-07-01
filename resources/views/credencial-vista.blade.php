<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credencial de {{ $personal->nombre }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .credencial {
                border: 1px solid #ccc;
                max-width: 100%;
                box-shadow: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100 p-4">
    <div class="max-w-md mx-auto">
        <div class="credencial bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-4 text-center">
                <h1 class="text-white text-xl font-bold">Credencial de Empleado</h1>
            </div>

            <div class="p-6">
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $personal->nombre }}</h2>
                    <p class="text-gray-600">{{ $personal->departamento ?? 'Sin departamento asignado' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">ID</p>
                        <p class="font-medium">{{ $personal->nro_identificacion }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">DNI</p>
                        <p class="font-medium">{{ $personal->dni ?? 'No registrado' }}</p>
                    </div>
                </div>

                <div class="flex flex-col items-center mb-6">
                    {!! QrCode::size(200)->generate($personal->nro_identificacion) !!}
                    <p class="mt-2 text-sm text-gray-500">ID: {{ $personal->nro_identificacion }}</p>
                </div>

                <div class="text-center text-sm text-gray-500">
                    <p>Esta credencial es personal e intransferible</p>
                    <p>Fecha de emisión: {{ date('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-center space-x-4 no-print">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Imprimir
            </button>
            <a href="{{ route('personal.credencial.pdf', ['id' => $personal->id]) }}"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Descargar PDF
            </a>
            <button onclick="window.close()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cerrar
            </button>
        </div>
    </div>
</body>

</html>
