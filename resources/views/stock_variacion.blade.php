<!DOCTYPE html>
<html>
<head>
    <title>Stock Movimientos</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Stock Movimientos</h1>

    @foreach($data as $stockId => $movements)
        <h2>Stock: {{ $movements->first()->stock->nombre }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Movimiento</th>
                    <th>Fecha</th>
                    <th>Personal</th>
                    <th>Certificación</th>
                    <th>Firma</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $movement)
                    <tr>
                        <td>{{ $movement->cantidad_movimiento }}</td>
                        <td>{{ $movement->fecha_movimiento->format('d/m/Y') }}</td>
                        <td>{{ $movement->personal->nombre }}</td>
                        <td>{{ $movement->certificacion }}</td>
                        <td>
                            @if($movement->firma)
                                <img src="{{$movement->firma}}" alt="Firma" style="width: 100px;">
                            @else
                                N/A
                            @endif
                        </td>
                        {{-- <td colspan="1">
                            <img src="{{ $movement->firma }}" alt="Firma del Trabajador" style="width: 150px; height: auto;">
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>
