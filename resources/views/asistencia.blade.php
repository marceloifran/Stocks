<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captura y Búsqueda Automática</title>
</head>
<body>
    <h1>Captura y Búsqueda Automática</h1>
    <video id="camera" autoplay playsinline></video>
    <canvas id="canvas" style="display: none;"></canvas>
    <button id="finalizarAsistenciaButton">Finalizar Asistencia</button>

    <div id="result"></div>
    <div id="validados">
        <h2>Personas Validadas</h2>
        <ul id="validadosList"></ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tesseract.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js"></script>

    <script>
        // Acceder al elemento de video y canvas
        const videoElement = document.getElementById('camera');
        const canvasElement = document.getElementById('canvas');
        const resultElement = document.getElementById('result');
        const validadosList = document.getElementById('validadosList');

        // Variable para mantener un registro de códigos coincidentes
        const codigosCoincidentes = new Set();

        // Obtener acceso a la cámara
        navigator.mediaDevices.getUserMedia({ video: true })
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
                                // Agregar el código a la lista de personas validadas
                                validadosList.innerHTML += `<li>${text}</li>`;
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

        // Finalizar la asistencia
        // ...

const finalizarAsistenciaButton = document.getElementById('finalizarAsistenciaButton');

// Variable para almacenar los datos de asistencia
const asistencia = [];

// Manejar el clic en el botón "Finalizar Asistencia"
finalizarAsistenciaButton.addEventListener('click', function () {
    // Obtener la fecha y hora actual
    const fechaHora = new Date().toLocaleString();

    // Recorrer los códigos validados y determinar quiénes están presentes y ausentes
    for (const codigo of codigosCoincidentes) {
        const presente = validadosList.querySelector(`li:contains("${codigo}")`) ? 'Sí' : 'No';
        asistencia.push({ codigo, presente, fechaHora });
    }

    // Enviar los datos de asistencia al servidor o realizar otras acciones
    // Por ejemplo, puedes enviarlos a través de una solicitud AJAX usando Axios
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

// ...

    </script>
</body>
</html>
