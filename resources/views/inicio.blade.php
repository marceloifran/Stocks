<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Software de gestión minera con asistencia QR, cálculo de horas, firma de EPPs y mucho más. Soporte multilingüe y reportes en diversos formatos.">
    <title>IFSIN Minería - Gestión y Control Minero</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <!-- Animaciones y Estilos Personalizados -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f5f7;
        }
        .hero-section {
            background-color: #0F172A;
            color: white;
            padding: 150px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-section h1 {
            font-size: 4rem;
            line-height: 1.2;
            font-weight: bold;
        }
        .hero-section p {
            font-size: 1.5rem;
            margin-top: 20px;
        }
        .hero-section .cta-btn {
            background-color: #FF6A3D;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .hero-section .cta-btn:hover {
            background-color: #FF4F1A;
            transform: scale(1.05);
        }
        .features-section {
            padding: 100px 0;
            background-color: #F9FAFB;
            text-align: center;
        }
        .feature-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 3rem;
            color: #0F172A;
        }
        .cta-section {
            background-color: #1E293B;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .cta-section .cta-btn {
            background-color: #FF6A3D;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .cta-section .cta-btn:hover {
            background-color: #FF4F1A;
        }
        .footer {
            background-color: #0F172A;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container mx-auto px-6">
            <h1 class="text-5xl font-extrabold">IFSIN Minería</h1>
            <p class="mt-6">Optimiza la gestión de tu mina con tecnología avanzada de asistencia QR, cálculo de horas, firma de EPPs y más.</p>
            <a href="#features" class="cta-btn mt-8">Explora los Módulos</a>
        </div>
        <div class="absolute top-0 left-0 w-full h-full">
            <svg class="absolute bottom-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#0F172A" fill-opacity="1" d="M0,160L40,176C80,192,160,224,240,224C320,224,400,192,480,160C560,128,640,96,720,122.7C800,149,880,235,960,240C1040,245,1120,171,1200,138.7C1280,107,1360,117,1400,122.7L1440,128L1440,320L1400,320C1360,320,1280,320,1200,320C1120,320,1040,320,960,320C880,320,800,320,720,320C640,320,560,320,480,320C400,320,320,320,240,320C160,320,80,320,40,320L0,320Z"></path>
            </svg>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold">Módulos Principales</h2>
            <p class="text-gray-600 mt-4">Soluciones adaptadas a la industria minera</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="feature-card">
                    <i class="bi bi-qr-code feature-icon"></i>
                    <h5 class="text-xl font-semibold mt-4">Asistencia mediante QR</h5>
                    <p class="text-gray-600 mt-2">Registra y controla la asistencia de empleados mediante tecnología QR.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-clock-history feature-icon"></i>
                    <h5 class="text-xl font-semibold mt-4">Cálculo de Horas</h5>
                    <p class="text-gray-600 mt-2">Automatiza el cálculo de horas trabajadas y gestiona turnos de forma eficiente.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-shield-check feature-icon"></i>
                    <h5 class="text-xl font-semibold mt-4">Firma de EPPs</h5>
                    <p class="text-gray-600 mt-2">Verifica el uso de EPPs con firmas electrónicas, asegurando la seguridad laboral.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-file-earmark-bar-graph feature-icon"></i>
                    <h5 class="text-xl font-semibold mt-4">Reportes Multiformato</h5>
                    <p class="text-gray-600 mt-2">Genera informes detallados en múltiples formatos, adaptados a tus necesidades.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-globe feature-icon"></i>
                    <h5 class="text-xl font-semibold mt-4">Soporte Multilingüe</h5>
                    <p class="text-gray-600 mt-2">Disponible en varios idiomas para facilitar su uso en equipos globales.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-info-circle feature-icon"></i>
                    <h5 class="text-xl font-semibold mt-4">Información de Amtafuegos</h5>
                    <p class="text-gray-600 mt-2">Accede a la información técnica de equipos de seguridad mediante QR.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold">Potencia tu Gestión Minera</h2>
            <p class="mt-4">Transforma la operación de tu mina con soluciones tecnológicas avanzadas y seguras.</p>
            <a href="#contact" class="cta-btn mt-8">Solicitar una Demostración</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container mx-auto px-6">
            <p>&copy; 2024 IFSIN Minería. Todos los derechos reservados.</p>
            <p><a href="#contact" class="text-gray-400">Contáctanos</a></p>
        </div>
    </footer>

    <!-- Bootstrap Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.js"></script>
</body>
</html>
