<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permiso Report</title>
</head>
<body>
    <h1>Permiso Report</h1>

    <div>
        <h2>Permiso Information:</h2>
        <p><strong>Tipo:</strong> {{ $permiso->tipo }}</p>
        <p><strong>Fecha:</strong> {{ $permiso->fecha }}</p>
    </div>

    <div>
        <h2>Personal Information:</h2>
        @foreach ($permiso->personal as $person)
            <p><strong>Nombre:</strong> {{ $person->nombre }}</p>
            <p><strong>Firma:</strong></p>
            <img src="{{ $person->firma }}" alt="Firma">
            <hr>
        @endforeach
    </div>
</body>
</html>
