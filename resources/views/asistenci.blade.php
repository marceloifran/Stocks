<!DOCTYPE html>
<html>
  <head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>Instascan QR Scanner</title>
  </head>
  <body>
    <div class="container">
                <h1 class="h1 text-center alert alert-success">Escaneo de Asistencia</h1>

                    {{-- <img class="text-center"src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="" style="width: 50px; height: 50px;"> --}}

                   <video style="width: 100%; height: 100%;" id="preview" class="text-center"></video>

                <div style="display: none" id="result" class="mt-3 fs-5"></div>
                <button class="btn btn-success" onclick="finalizarAsistencia()">Finalizar Asistencia</button>
            <div class="col-lg-4">
                {{-- <h2 class="fs-4">Personas Escaneadas</h2> --}}
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

    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
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
          scanner.start(cameras[0]);
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
    // Función para validar el código
function validarCodigo(text) {
  if (asistencia.has(text)) {
    // Si el código ya está en la lista de asistencia, mostrar un mensaje de error
    mostrarError("Código ya escaneado.");
    return;
  }

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
        asistencia.add(text);
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
