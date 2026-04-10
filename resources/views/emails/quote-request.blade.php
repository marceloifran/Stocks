<!DOCTYPE html>
<html>
<head>
    <title>Pedido de Cotización</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px;">
        <h2 style="color: #2563eb;">Pedido de Cotización</h2>
        <p>Estimado proveedor,</p>
        <p><strong>{{ $companyName }}</strong> está solicitando una cotización para el siguiente producto:</p>
        
        <div style="background-color: #f9fafb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Producto:</strong> {{ $request->product_name }}</p>
            <p><strong>Cantidad:</strong> {{ $request->quantity }}</p>
            @if($request->notes)
                <p><strong>Notas adicionales:</strong><br>{{ $request->notes }}</p>
            @endif
        </div>

        <p>Por favor, envíenos su mejor propuesta respondiendo a este correo electrónico.</p>
        <p>Agradecemos su pronta respuesta.</p>
        <br>
        <p>Atentamente,<br>
        <strong>{{ $userName }}</strong><br>
        {{ $companyName }}</p>
    </div>
</body>
</html>
