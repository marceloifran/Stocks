<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Personal</title>

    <!-- Agrega el enlace a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Agrega estilos personalizados -->
    <style>
        /* Estilos para las tarjetas de Bootstrap */
        .persona-card {
            width: 30rem;
            margin: 20px;
            background-color: #1bda21;
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .imagen-personal {
            width: 100px;
            height: auto;
            margin-bottom: 10px; /* Espacio entre la imagen y el número */
            float: left
        }

        /* Estilos personalizados para el número de identificación */
        .numero-identificacion {
            font-family: 'Poppins', sans-serif;
            font-size: 70px; /* Reducido el tamaño para un aspecto mejor */
            color: #000;
            float: right
        }

        /* Estilos personalizados para el título (nombre) */
        .card-title {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            color: #000;
        }

        /* Estilos personalizados para el contenedor del texto (nombre) */
        .nombre-container {
            display: flex;
            justify-content: center; /* Centra horizontalmente */
            align-items: center; /* Centra verticalmente */
            flex-grow: 1; /* Toma todo el espacio disponible verticalmente */
        }
    </style>
</head>
<body>
    <div class="container">
        @foreach ($personal as $persona)
        <div class="card persona-card">
            <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="" class="imagen-personal">
            <h1 class="numero-identificacion">{{ $persona->nro_identificacion }}</h1>
            <div class="nombre-container">
                <h6 class="card-title">{{ $persona->nombre }}</h6>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Agrega el enlace a Bootstrap JS y jQuery (opcional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
