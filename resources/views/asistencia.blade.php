<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asistencia</title>
    <style>
        #reader {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        #camera-switch-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }

        #fin {
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center h1 alert alert-info">Escaneo de Asistencia</h1>
        <div id="reader-container" class="position-relative">
            <button id="camera-switch-btn" class="btn btn-primary">
                <i class="bi bi-camera"></i> Cambiar cámara
            </button>
            <div id="reader" class="text-center"></div>
        </div>
        <div style="display: none" id="result" class="mt-3 fs-5"></div>
        <button id="fin" class="btn btn-info" onclick="finalizarAsistencia()">Finalizar Asistencia</button>
        <div class="form-group">
            <h1 class="alert alert-info">Personas Validadas: <span id="contadorPersonas">0</span></h1>
            <h3 id="validadosList"></h3>
            <div class="form-group">
                <label for="tipoAsistencia" class="fs-4">Tipo de Asistencia</label>
                <select id="tipoAsistencia" class="form-control">
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                </select>
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
            const asistencia = new Set();
            const codigosCoincidentes = new Set();
            const estadoSelect = document.getElementById('tipoAsistencia');
            const asistenciaData = [];
            let currentCamera = 'environment';

            let html5QrCode;

            function initializeScanner() {
                html5QrCode = new Html5Qrcode("reader");
                startScanner();
            }

            function startScanner() {
                const config = {
                    fps: 1,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    aspectRatio: 1.0,
                    videoConstraints: {
                        facingMode: currentCamera,
                        // Desactivar el efecto espejo
                        mirror: false
                    }
                };

                html5QrCode.start({
                        facingMode: currentCamera
                    },
                    config,
                    onScanSuccess
                ).catch((err) => {
                    console.error("Error al iniciar el escáner:", err);
                });
            }

            function onScanSuccess(decodedText, decodedResult) {
                // Pausar el escáner temporalmente para evitar múltiples escaneos
                html5QrCode.pause();

                if (/^\d+$/.test(decodedText)) {
                    validarCodigo(decodedText);
                } else {
                    mostrarError("Código inválido: no es un número.");
                    // Reanudar el escáner después de mostrar el error
                    setTimeout(() => html5QrCode.resume(), 2000);
                }
            }

            document.getElementById('camera-switch-btn').addEventListener('click', () => {
                html5QrCode.stop().then(() => {
                    currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
                    startScanner();
                }).catch((err) => {
                    console.error("Error al cambiar de cámara:", err);
                });
            });

            window.onload = initializeScanner;

            function finalizarAsistencia() {
                const estado = estadoSelect.value;
                console.log('Datos de Asistencia:', asistenciaData);

                if (asistenciaData.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sin datos',
                        text: 'No hay asistencias para guardar',
                    });
                    return;
                }

                Swal.fire({
                    title: '¿Guardar asistencia?',
                    text: `Se guardarán ${asistenciaData.length} registros`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('/guardar-asis', {
                                asistencia: asistenciaData
                            })
                            .then(function(response) {
                                console.log('Asistencia guardada exitosamente.', response.data);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Guardado',
                                    text: 'Asistencia guardada exitosamente',
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
                                console.error('Error al guardar la asistencia:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al guardar la asistencia',
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

                axios.post('/buscar-coincidencias', {
                        codigo: text
                    })
                    .then(function(response) {
                        console.log(response.data);
                        const coincidencias = response.data.coincidencias;
                        if (coincidencias.length === 0) {
                            mostrarError("Código no encontrado en la base de datos.");
                            // Reanudar el escáner después de mostrar el error
                            setTimeout(() => html5QrCode.resume(), 2000);
                        } else {
                            const persona = coincidencias[0];
                            mostrarValidado(persona.nro_identificacion, persona.nombre);

                            Swal.fire({
                                icon: 'success',
                                title: 'Validado',
                                text: `Persona validada: ${persona.nombre}`,
                                timer: 1500,
                                showConfirmButton: false,
                            }).then(() => {
                                // Reanudar el escáner después de mostrar el mensaje
                                html5QrCode.resume();
                            });

                            asistencia.add(text);
                            codigosCoincidentes.add(text);

                            const contadorPersonas = document.getElementById('contadorPersonas');
                            contadorPersonas.textContent = codigosCoincidentes.size;
                            asistenciaData.push({
                                codigo: text,
                                fecha,
                                hora: `${hora}:${minuto}:${segundo}`,
                                estado: estadoSelect.value,
                                timestamp: new Date().toISOString(),
                            });
                            console.log('Datos de Asistencia:', asistenciaData);
                        }
                    })
                    .catch(function(error) {
                        console.error(error);
                        mostrarError("Error al buscar coincidencias en la base de datos.");
                        // Reanudar el escáner después de mostrar el error
                        setTimeout(() => html5QrCode.resume(), 2000);
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
                    timer: 2000,
                    showConfirmButton: false,
                });
            }

            function mostrarValidado(nroIdentificacion, nombre) {
                const validadosList = document.getElementById('validadosList');
                validadosList.innerHTML = '';
                const listItem = document.createElement('h2');
                listItem.classList.add('alert', 'alert-success');
                listItem.textContent = `${nroIdentificacion} - ${nombre} (Validado)`;
                validadosList.appendChild(listItem);
            }
        </script>
</body>

</html>
