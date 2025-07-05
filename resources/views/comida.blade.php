<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Control de Comidas</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
        }

        #reader {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        #camera-switch-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #fin {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 10px 30px;
            font-weight: bold;
        }

        .scan-region-highlight {
            border-radius: 10px !important;
            border: 3px solid #28a745 !important;
        }

        .validated-item {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            color: white;
            font-size: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .scanner-laser {
            position: absolute;
            width: 100%;
            height: 2px;
            background: rgba(255, 0, 0, 0.7);
            z-index: 11;
            box-shadow: 0 0 4px rgba(255, 0, 0, 0.7);
            animation: scanning 2s infinite;
        }

        @keyframes scanning {
            0% {
                top: 20%;
            }

            50% {
                top: 80%;
            }

            100% {
                top: 20%;
            }
        }

        .food-type {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .food-type input {
            display: none;
        }

        .food-type label {
            flex: 1;
            min-width: 120px;
            padding: 12px;
            text-align: center;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .food-type input:checked+label {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .food-type label:hover {
            border-color: #0d6efd;
        }

        .food-type label[for="desayuno"] {
            border-left: 4px solid #ffc107;
        }

        .food-type label[for="almuerzo"] {
            border-left: 4px solid #fd7e14;
        }

        .food-type label[for="merienda"] {
            border-left: 4px solid #20c997;
        }

        .food-type label[for="cena"] {
            border-left: 4px solid #6f42c1;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <h1 class="h1 text-center alert alert-primary shadow-sm">Escaneo de Comidas</h1>
        <div id="reader-container" class="position-relative mb-4">
            <button id="camera-switch-btn" class="btn btn-primary shadow">
                <i class="bi bi-camera"></i>
            </button>
            <div id="reader" class="text-center"></div>
            <div id="scanner-overlay" class="scanner-overlay d-none">
                <div>Procesando...</div>
            </div>
            <div id="scanner-laser" class="scanner-laser"></div>
        </div>
        <div style="display: none" id="result" class="mt-3 fs-5"></div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <div class="form-group mb-0">
                    <label class="fs-5 mb-2">Tipo de Comida</label>
                    <div class="food-type">
                        <input type="radio" name="tipoComida" id="desayuno" value="desayuno" checked>
                        <label for="desayuno">Desayuno</label>

                        <input type="radio" name="tipoComida" id="almuerzo" value="almuerzo">
                        <label for="almuerzo">Almuerzo</label>

                        <input type="radio" name="tipoComida" id="merienda" value="merienda">
                        <label for="merienda">Merienda</label>

                        <input type="radio" name="tipoComida" id="cena" value="cena">
                        <label for="cena">Cena</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h2 class="card-title">Personas Validadas: <span id="contadorPersonas" class="badge bg-success">0</span>
                </h2>
                <div id="validadosList" class="mt-3"></div>
            </div>
            <div class="card-footer">
                <button id="fin" class="btn btn-primary w-100" onclick="finalizarComida()">
                    Finalizar Registro
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script type="text/javascript">
        const comida = new Set();
        const codigosCoincidentes = new Set();
        const comidaData = [];
        let currentCamera = 'environment';
        const scannerOverlay = document.getElementById('scanner-overlay');

        let html5QrCode;

        function initializeScanner() {
            html5QrCode = new Html5Qrcode("reader");
            startScanner();
        }

        function startScanner() {
            const config = {
                fps: 10, // Mayor velocidad de frames para escaneo más rápido
                qrbox: {
                    width: 300,
                    height: 300
                }, // Área de escaneo más grande
                aspectRatio: 1.0,
                formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE, Html5QrcodeSupportedFormats.CODE_128],
                videoConstraints: {
                    facingMode: currentCamera,
                    // Configuración para mejor rendimiento
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    },
                    frameRate: {
                        ideal: 15,
                        min: 10
                    },
                    mirror: false
                },
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true // Usar API nativa si está disponible
                }
            };

            // Mostrar un mensaje mientras se inicia la cámara
            Swal.fire({
                title: 'Iniciando cámara',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            html5QrCode.start({
                    facingMode: currentCamera
                },
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                Swal.close();
            }).catch((err) => {
                console.error("Error al iniciar el escáner:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de cámara',
                    text: 'No se pudo acceder a la cámara. Verifique los permisos.',
                });
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Mostrar overlay de procesamiento
            scannerOverlay.classList.remove('d-none');

            // Reproducir sonido de éxito
            const beepSound = new Audio(
                'data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA/+M4wAAAAAAAAAAAAEluZm8AAAAPAAAAAwAAAbAAkJCQkJCQkJCQkJCQkJCQwMDAwMDAwMDAwMDAwMDAwMD//////////////////8AAAA5TEFNRTMuMTAwAZYAAAAAAAAAABQ4JAMGQgAAQAAAAwCgrxWwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/jOMAAAANIAAAAAExBTUUzLjEwMFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/jOMAAAANIAAAAAFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV'
                );
            beepSound.play();

            // Pausar el escáner temporalmente para evitar múltiples escaneos
            html5QrCode.pause();

            console.log("Código escaneado:", decodedText);

            // Eliminar la validación estricta de solo números
            validarCodigo(decodedText);
        }

        function onScanFailure(error) {
            // No hacemos nada en caso de error de escaneo para evitar mensajes innecesarios
        }

        document.getElementById('camera-switch-btn').addEventListener('click', () => {
            Swal.fire({
                title: 'Cambiando cámara',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            html5QrCode.stop().then(() => {
                currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
                startScanner();
            }).catch((err) => {
                console.error("Error al cambiar de cámara:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cambiar la cámara.',
                });
            });
        });

        window.onload = initializeScanner;

        function getTipoComida() {
            const radioButtons = document.querySelectorAll('input[name="tipoComida"]');
            for (const radioButton of radioButtons) {
                if (radioButton.checked) {
                    return radioButton.value;
                }
            }
            return 'desayuno'; // Default
        }

        function finalizarComida() {
            const tipo = getTipoComida();
            console.log('Datos de Comida:', comidaData);

            if (comidaData.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin datos',
                    text: 'No hay registros de comida para guardar',
                });
                return;
            }

            Swal.fire({
                title: '¿Guardar registro de comidas?',
                text: `Se guardarán ${comidaData.length} registros`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Guardando...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    axios.post('/guardar-comida', {
                            comida: comidaData
                        })
                        .then(function(response) {
                            console.log('Comida guardada exitosamente.', response.data);
                            Swal.fire({
                                icon: 'success',
                                title: 'Guardado',
                                text: 'Registro de comidas guardado exitosamente',
                                timer: 2000,
                                showConfirmButton: false,
                            }).then(() => {
                                codigosCoincidentes.clear();
                                document.getElementById('validadosList').innerHTML = '';
                                document.getElementById('contadorPersonas').textContent = '0';
                                previousPageURL = document.referrer;
                                window.location.href = previousPageURL;
                            });
                        })
                        .catch(function(error) {
                            console.error('Error al guardar la comida:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al guardar el registro de comidas',
                            });
                        });
                }
            });
        }

        function validarCodigo(text) {
            if (codigosCoincidentes.has(text)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Persona ya validada`,
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => {
                    // Reanudar el escáner después de mostrar el mensaje
                    scannerOverlay.classList.add('d-none');
                    html5QrCode.resume();
                });
                console.log("Código ya escaneado.");
                return;
            }

            const fechaHora = new Date();
            const options = {
                timeZone: 'America/Argentina/Buenos_Aires',
                hour12: false
            };
            const fechaHoraArgentina = fechaHora.toLocaleString('es-AR', options).split(', ');
            const fecha = fechaHoraArgentina[0];
            const horaMinutoSegundo = fechaHoraArgentina[1].split(':');
            const hora = horaMinutoSegundo[0];
            const minuto = horaMinutoSegundo[1];
            const segundo = fechaHora.getSeconds();

            // Mostrar información sobre el código escaneado
            console.log("Buscando código:", text);

            axios.post('/buscar-coincidencias', {
                    codigo: text
                })
                .then(function(response) {
                    console.log("Respuesta del servidor:", response.data);
                    const coincidencias = response.data.coincidencias;
                    if (coincidencias.length === 0) {
                        mostrarError("Código no encontrado en la base de datos.");
                        // Reanudar el escáner después de mostrar el error
                        setTimeout(() => {
                            scannerOverlay.classList.add('d-none');
                            html5QrCode.resume();
                        }, 1500);
                    } else {
                        const persona = coincidencias[0];
                        const tipoComida = getTipoComida();
                        mostrarValidado(persona.nro_identificacion, persona.nombre, tipoComida);

                        Swal.fire({
                            icon: 'success',
                            title: 'Validado',
                            text: `${persona.nombre}`,
                            timer: 1200,
                            showConfirmButton: false,
                            position: 'top-end',
                            toast: true
                        }).then(() => {
                            // Reanudar el escáner después de mostrar el mensaje
                            scannerOverlay.classList.add('d-none');
                            html5QrCode.resume();
                        });

                        comida.add(text);
                        codigosCoincidentes.add(text);

                        const contadorPersonas = document.getElementById('contadorPersonas');
                        contadorPersonas.textContent = codigosCoincidentes.size;
                        comidaData.push({
                            codigo: text,
                            fecha,
                            hora: `${hora}:${minuto}:${segundo}`,
                            tipo_comida: tipoComida,
                            timestamp: new Date().toISOString(),
                        });
                        console.log('Datos de Comida:', comidaData);
                    }
                })
                .catch(function(error) {
                    console.error("Error en la solicitud:", error);
                    mostrarError("Error al buscar coincidencias en la base de datos.");
                    // Reanudar el escáner después de mostrar el error
                    setTimeout(() => {
                        scannerOverlay.classList.add('d-none');
                        html5QrCode.resume();
                    }, 1500);
                });
        }

        function mostrarError(message) {
            const resultElement = document.getElementById('result');
            resultElement.style.display = 'block';
            resultElement.innerHTML = `<p class="alert alert-danger">${message}</p>`;

            // También mostrar un SweetAlert
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                timer: 1500,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        }

        function mostrarValidado(nroIdentificacion, nombre, tipoComida) {
            const validadosList = document.getElementById('validadosList');

            // Crear nuevo elemento para la persona validada
            const listItem = document.createElement('div');
            listItem.classList.add('alert', 'mb-2', 'validated-item');

            // Asignar clase de color según el tipo de comida
            switch (tipoComida) {
                case 'desayuno':
                    listItem.classList.add('alert-warning');
                    break;
                case 'almuerzo':
                    listItem.classList.add('alert-success');
                    break;
                case 'merienda':
                    listItem.classList.add('alert-info');
                    break;
                case 'cena':
                    listItem.classList.add('alert-secondary');
                    break;
                default:
                    listItem.classList.add('alert-light');
            }

            // Crear contenido con formato
            const timestamp = new Date().toLocaleTimeString('es-AR', {
                hour: '2-digit',
                minute: '2-digit'
            });
            const iconMap = {
                'desayuno': '☕',
                'almuerzo': '🍽️',
                'merienda': '🥪',
                'cena': '🍲'
            };

            listItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${nombre}</strong>
                        <div class="text-muted small">ID: ${nroIdentificacion}</div>
                    </div>
                    <div>
                        <span class="badge bg-primary">${iconMap[tipoComida]} ${tipoComida}</span>
                        <div class="text-muted small">${timestamp}</div>
                    </div>
                </div>
            `;

            // Insertar al principio de la lista
            validadosList.insertBefore(listItem, validadosList.firstChild);
        }
    </script>
</body>

</html>
