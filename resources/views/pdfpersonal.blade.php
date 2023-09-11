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
            width: 18rem;
            margin: 20px;
            background-color: #1bda21;
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .imagen-personal {
            width: 100px;
            height: auto;
            margin: 0 auto;
            display: block;
        }

        /* Estilos personalizados para el título */
        .card-title {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            color: #000;
        }


        /* Estilos personalizados para el número de identificación */
        .card-text {
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        @foreach ($personal as $persona)
        <div class="card persona-card">
            <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="" s class="imagen-personal">
            <div class="card-body">
                <h5 class="card-title">{{ $persona->nombre }}</h5>

                <p class="card-text">{{ $persona->nro_identificacion }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Agrega el enlace a Bootstrap JS y jQuery (opcional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
