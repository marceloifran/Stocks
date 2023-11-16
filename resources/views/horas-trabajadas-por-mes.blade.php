
    <div class="container">
        <h1>Horas trabajadas por mes</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Horas trabajadas</th>
                    <th>Horas extras</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($horasTrabajadasPorMes as $mesAnio => $horasTrabajadas)
                    <tr>
                        <td>{{ $mesAnio }}</td>
                        <td>{{ $horasTrabajadas }}</td>
                        <td>{{ $horasExtrasPorMes[$mesAnio] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

