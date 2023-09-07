<!-- Personal PDF -->
<html>
<head>
    <title>Informe de Persona</title>

</body>
</head>
<style>
    /* Estilos para las líneas divisorias */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    td {
        border: 1px solid black; /* Borde sólido de 1px de grosor */
        padding: 8px; /* Espaciado interno para el contenido */
        text-align: center; /* Alineación del texto al centro */
    }
    .texto-centrado {
    text-align: center;
}
table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
</style>
<body>
    <div class="container">

        <table style="height: 25px">
            <tr>
                <td style="text-align: center" colspan="10">
                    CONSTANCIA DE ENTREGA DE ROPA Y TRABAJO Y ELEMENTOS DE PROTECCION PERSONAL <br>
                    (Resolucion S.R.T Nº 299/2011)
                </td>
                <td style="text-align: right;">
                    <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="" style="width: 50px; height: 50px;">
                </td>

            </tr>



            <tr>
                <td colspan="1">Razón Social</td>
                <td colspan="4">BMI S.A.</td>
                <td colspan="2">C.U.I.T. Nº:</td>
                <td colspan="4">30-69070075-8</td>
            </tr>
            <tr>
                <td colspan="1">Dirección:</td>
                <td colspan="4">Av. Alfredo Palacios Nº 2430</td>
                <td colspan="1">Localidad:</td>
                <td colspan="1">Salta</td>
                <td colspan="1">CP.:</td>
                <td colspan="1">4400</td>
                <td colspan="1">Provincia:</td>
                <td colspan="1">Salta</td>
            </tr>
            <tr>
                <td colspan="2">Apellido y Nombre del Trabajador: </td>
                <td colspan="4">{{ $persona->nombre }}</td>
                <td colspan="2">D.N.I Nº:</td>
                <td colspan="3">{{ $persona->dni }}</td>

            </tr>
            <tr>
                <td colspan="5">Breve descripción del/lospuestos/s de trabajo en el/los cual/es se desempeña el trabajador:</td>
                <td colspan="6">Elementos de Protección Personal necesarios para el trabajador, según el/los puesto/s de trabajo:</td>
            </tr>
            <tr>
                <td colspan="5">Operario de Tareas Generales en Obra: Excavación Manual y Movimiento de Sueldos -Corte y Armado de Hierros - Armado de Encofrados - Elaboración, vertido y vibrado de Hº Aº - Trabajos de Albañilería en General.-</td>
                <td colspan="6">Casco de Seguridad / Gafas de Seguridad Transparentes / Guantes de Vaqueta / Guantes de Acrilonitrilo / Mascarilla libre de mantenimiento de polvos / Botines de Seguridad con Puntera / Protectores Auditivos. -</td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th style="font-size: 12px;">Nº</th>
            <th style="font-size: 12px;"></th>
            <th style="font-size: 12px;">TIPO/MODELO</th>
            <th style="font-size: 12px;">MARCA</th>
            <th style="font-size: 12px;">POSEE CERTIFICACIÓN (SI/NO)</th>
            <th style="font-size: 12px;">CANTIDAD</th>
            <th style="font-size: 12px;">FECHA DE ENTREGA</th>
            <th style="font-size: 12px;">FIRMA DEL TRABAJADOR</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($persona->stockMovement as $movement)
                <tr>
                    <td colspan="1">{{ $movement->id }}</td>
                    <td colspan="1">{{ $movement->stock->nombre }}</td>
                    <td colspan="1">{{ $movement->tipo }}</td>
                    <td colspan="1">{{ $movement->marca }}</td>
                    <td colspan="1">{{ $movement->certificacion }}</td>
                    <td colspan="1">{{ $movement->cantidad_movimiento }}</td>
                    <td colspan="1">{{ $movement->fecha_movimiento }}</td>
                    <td colspan="1"></td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
