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
        <div class="credencial bg-white rounded-lg shadow-lg overflow-hidden relative">
            <div class="p-4 text-center border-b">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo del Sistema" class="h-12 mx-auto">
                <div class="absolute top-2 right-4 font-bold text-gray-700">CREDENCIAL</div>
            </div>

            <div class="p-6">
                <!-- QR Code en el centro -->
                <div class="flex justify-center mb-6">
                    {!! QrCode::size(180)->generate($personal->nro_identificacion) !!}
                </div>

                <!-- Información del empleado -->
                <div class="space-y-4">
                    <div class="border-b pb-2">
                        <p class="text-sm text-gray-500">Nombre:</p>
                        <p class="font-bold text-gray-800">{{ $personal->nombre }}</p>
                    </div>

                    <div class="border-b pb-2">
                        <p class="text-sm text-gray-500">DNI:</p>
                        <p class="font-bold text-gray-800">{{ $personal->dni ?? 'No registrado' }}</p>
                    </div>

                    <div class="border-b pb-2">
                        <p class="text-sm text-gray-500">Departamento:</p>
                        <p class="font-bold text-gray-800">{{ $personal->departamento ?? 'Sin departamento asignado' }}
                        </p>
                    </div>

                    <div class="border-b pb-2">
                        <p class="text-sm text-gray-500">ID:</p>
                        <p class="font-bold text-gray-800">{{ $personal->nro_identificacion }}</p>
                    </div>
                </div>

                <div class="mt-6 text-center text-sm text-gray-500">
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
