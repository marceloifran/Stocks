<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captura y Búsqueda Automática</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">


</head>
<body>
   <div class="container mt-4">
        <h1 class="display-4 text-center">Captura y Búsqueda Automática</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <video id="camera" autoplay playsinline class="w-100"></video>
                    <canvas id="canvas" style="display: none;"></canvas>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado:</label>
                    <select id="estado" class="form-select">
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </select>
                </div>
                <button id="finalizarAsistenciaButton" class="btn btn-primary">Finalizar Asistencia</button>
            </div>
            <div class="col-md-6">
                <div id="result"></div>
                <div id="validados">
                    <h2>Personas Validadas</h2>
                    <ul id="validadosList" class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tesseract.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>


    <script>
        // Acceder al elemento de video y canvas
        const videoElement = document.getElementById('camera');
        const canvasElement = document.getElementById('canvas');
        const resultElement = document.getElementById('result');
        const validadosList = document.getElementById('validadosList');

        const estadoSelect = document.getElementById('estado');




        // Variable para mantener un registro de códigos coincidentes
        const codigosCoincidentes = new Set();

        // Variable para mantener un registro de personas validadas
        const personasValidadas = {};

        // Obtener acceso a la cámara
        navigator.mediaDevices.getUserMedia({
    video: {
        facingMode: 'environment' // Utilizar la cámara principal
    }
})
.then(function (stream) {
    videoElement.srcObject = stream;
})
.catch(function (error) {
    console.error('Error al acceder a la cámara:', error);
});

        // Función para procesar la imagen y validar el número
        function procesarImagen() {
            const context = canvasElement.getContext('2d');
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            context.drawImage(videoElement, 0, 0);

            // Procesar la imagen y reconocer números utilizando Tesseract.js
            Tesseract.recognize(
                canvasElement,
                'eng', // Idioma para el reconocimiento
                { logger: m => console.log(m) } // Opciones y registro de eventos
            ).then(({ data: { text } }) => {
                // "text" contiene los números reconocidos en la imagen
                console.log('Nro:', text);

                if (!codigosCoincidentes.has(text)) {
                    // Verificar si el código ya ha sido validado
                    // Realizar una solicitud al servidor para buscar coincidencias
                    axios.post('/buscar-coincidencias', { numeros: text })
                        .then(function (response) {
                            console.log(response.data);
                            const coincidencias = response.data.coincidencias; // Acceder a la clave "coincidencias"
                            if (coincidencias.length === 0) {
                                // Si no se encontraron coincidencias, mostrar un mensaje
                                resultElement.innerHTML = '<p>No se encontraron coincidencias.</p>';
                            } else {
                                // Mostrar las coincidencias en la interfaz de usuario o realizar otras acciones
                                resultElement.innerHTML = `<p>Coincidencias:</p><ul>${coincidencias.map(c => `<li>${c.nombre}</li>`).join('')}</ul>`;
                                // Agregar el código a la lista de códigos coincidentes
                                codigosCoincidentes.add(text);
                                // Agregar la persona validada al registro
                                personasValidadas[text] = coincidencias;
                                // Marcar la persona como validada en la lista
                                validadosList.innerHTML += `<li>${text} (Validado)</li>`;
                            }
                        })
                        .catch(function (error) {
                            console.error(error);
                            // Manejar el error de la solicitud
                            resultElement.innerHTML = '<p>Error al buscar coincidencias.</p>';
                        });
                } else {
                    // Si el código ya ha sido validado anteriormente, mostrar un mensaje
                    resultElement.innerHTML = '<p>Código ya validado anteriormente.</p>';
                }
            });
        }

        // Establecer un bucle para procesar automáticamente la imagen en intervalos regulares
        setInterval(procesarImagen, 5000); // Procesar cada 5 segundos (ajustable según tus necesidades)

        const finalizarAsistenciaButton = document.getElementById('finalizarAsistenciaButton');
finalizarAsistenciaButton.addEventListener('click', function () {
    // Obtener el valor del estado seleccionado aquí
    const estado = estadoSelect.value;

    const fechaHora = new Date();
    const fecha = fechaHora.toISOString().split('T')[0];
const hora = fechaHora.toISOString().split('T')[1].split('.')[0];

    // Crear un arreglo para almacenar los datos de asistencia
    const asistencia = [];

    // Recorrer los códigos validados y determinar quiénes están presentes y ausentes
    for (const codigo of codigosCoincidentes) {
        const presente = personasValidadas[codigo] ? 'Sí' : 'No';
        console.log(`Código: ${codigo}, Presente: ${presente}, Coincidencias: `, personasValidadas[codigo]);
        asistencia.push({ codigo, fecha,hora, presente, estado }); // Incluir el estado en los datos de asistencia
    }

    // Mostrar los datos de asistencia en la consola antes de enviarlos
    console.log('Datos de Asistencia:', asistencia);

    // Enviar los datos de asistencia al servidor o realizar otras acciones
    axios.post('/guardar-asistencia', { asistencia })
        .then(function (response) {
            console.log('Asistencia guardada exitosamente.');
            // Restablecer la lista de códigos coincidentes y la lista de personas validadas
            codigosCoincidentes.clear();
            validadosList.innerHTML = '';
        })
        .catch(function (error) {
            console.error('Error al guardar la asistencia:', error);
        });
});
    </script>
</body>
</html>
