<!DOCTYPE html>
<html>
  <head>
    <link
    rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
  />
    <title>Instascan QR Scanner</title>
  </head>
  <body>
    <style>
          /* Estilo para el contenedor del recuadro de escaneo */
  .qr-scanner {
    position: relative;
    width: 300px; /* Ajusta el tamaño del recuadro según tus necesidades */
    height: 300px; /* Ajusta el tamaño del recuadro según tus necesidades */
    overflow: hidden;
    border: 2px solid #000;
    margin: 0 auto;
  }

  /* Estilo para la animación de escaneo */
  .scan-animation {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.8));
    animation: scan 2s infinite linear;
  }

  /* Animación de escaneo */
  @keyframes scan {
    0% {
      transform: translateY(-100%);
    }
    100% {
      transform: translateY(100%);
    }
  }
    </style>
    <div class="container">
            <div >
                <h1 class="h1 text-center">Escaneo de Asistencia</h1>
                <div class="center-image">
                    <img   src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="" style="width: 50px; height: 50px;">
                   </div>
                   <video style="width: 100%; transform: rotateY(180deg);" id="preview" class="text-center"></video>
                <div id="result" class="mt-3 fs-5"></div>
                <button class="btn btn-primary mt-3" onclick="finalizarAsistencia()">Finalizar Asistencia</button>
            </div>
            <div class="col-lg-4">
                <h2 class="fs-4">Personas Escaneadas</h2>
                <ul id="listaAsistencia" class="fs-5"></ul>
                <h2 class="fs-4">Personas Validadas</h2>
                <ul id="validadosList" class="fs-5"></ul>

                <div class="form-group">
                    <label for="tipoAsistencia" class="fs-4">Tipo de Asistencia:</label>
                    <select id="tipoAsistencia" class="form-control fs-5">
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </select>
                </div>
            </div>
    </div>
>

    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script type="text/javascript">
      let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

      const asistencia = new Set(); // Usar un Set para evitar duplicados de códigos
      const codigosCoincidentes = new Set();
      const estadoSelect = document.getElementById('tipoAsistencia');



      // Cuando se escanea un código QR, muestra la información en el div "result" y agrega a la lista
      scanner.addListener('scan', function (text) {
        document.getElementById('result').innerText = text;

        // Verificar si el código es un número válido
        if (/^\d+$/.test(text)) {
          // Validar el código solo si es un número
          validarCodigo(text);
        } else {
          // Mostrar mensaje de error si no es un número válido
          mostrarError("Código inválido: no es un número.");
        }
      });

      // Obtener las cámaras disponibles
      Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
          scanner.start(cameras[1]);
        } else {
          console.error('No cameras found.');
        }
      }).catch(function (e) {
        console.error(e);
      });

      // Función para finalizar la asistencia
// Función para finalizar la asistencia
function finalizarAsistencia() {
  // Convertir el conjunto (Set) de códigos coincidentes a un array
  const codigosArray = Array.from(codigosCoincidentes);

  console.log('Códigos coincidentes:', codigosArray)

  // Obtener el valor del estado seleccionado
  const estado = estadoSelect.value;

  // Obtener la fecha y hora actual en el formato deseado
  const fechaHora = new Date();
const options = { timeZone: 'America/Argentina/Buenos_Aires', hour12: false };
const fechaHoraArgentina = fechaHora.toLocaleString('es-AR', options).split(', ');

const fecha = fechaHoraArgentina[0];
const hora = fechaHoraArgentina[1];

  // Crear un arreglo para almacenar los datos de asistencia
  const asistenciaData = [];

  // Recorrer los códigos validados y agregarlos a la lista de asistencia
  for (const codigo of codigosArray) {
    asistenciaData.push({ codigo, fecha, hora, estado });
  }

  // Mostrar los datos de asistencia en la consola antes de enviarlos
  console.log('Datos de Asistencia:', asistenciaData);

  // Enviar los datos de asistencia al servidor o realizar otras acciones
  axios.post('/guardar-asistencia', { asistencia: asistenciaData })
    .then(function (response) {
      console.log('Asistencia guardada exitosamente.');
      // Restablecer la lista de códigos coincidentes y la lista de personas validadas
      codigosCoincidentes.clear();
      validadosList.innerHTML = '';
    })
    .catch(function (error) {
      console.error('Error al guardar la asistencia:', error);
    });
}




      // Función para validar el código
      function validarCodigo(text) {
        // Realizar una solicitud al servidor para buscar coincidencias
        axios.post('/buscar-coincidencias', { codigo: text })
          .then(function (response) {
            console.log(response.data);
            const coincidencias = response.data.coincidencias; // Acceder a la clave "coincidencias"
            if (coincidencias.length === 0) {
              // Si no se encontraron coincidencias, mostrar un mensaje
              mostrarError("Código no encontrado en la base de datos.");
            } else {
              // Mostrar las coincidencias en la interfaz de usuario
              const persona = coincidencias[0]; // Tomar la primera coincidencia
              mostrarValidado(persona.nro_identificacion, persona.nombre);
              // Agregar el código a la lista de asistencia
              codigosCoincidentes.add(text);
              actualizarLista(); // Actualizar la lista en la interfaz
            }
          })
          .catch(function (error) {
            console.error(error);
            // Manejar el error de la solicitud
            mostrarError("Error al buscar coincidencias en la base de datos.");
          });
      }

      // Función para mostrar un mensaje de error
      function mostrarError(message) {
        const resultElement = document.getElementById('result');
        resultElement.innerHTML = `<p>Error: ${message}</p>`;
      }

      // Función para mostrar un código validado en la lista de personas validadas
      function mostrarValidado(nroIdentificacion, nombre) {
        const validadosList = document.getElementById('validadosList');
        const listItem = document.createElement('li');
        listItem.textContent = `${nroIdentificacion} - ${nombre} (Validado)`;
        validadosList.appendChild(listItem);
      }

      // Función para actualizar la lista de asistencia en la interfaz
      function actualizarLista() {
        const listaAsistencia = document.getElementById('listaAsistencia');
        listaAsistencia.innerHTML = ''; // Borra la lista actual

        asistencia.forEach((codigo) => {
          const listItem = document.createElement('li');
          listItem.textContent = codigo;
          listaAsistencia.appendChild(listItem);
        });
      }
    </script>
  </body>
</html>
