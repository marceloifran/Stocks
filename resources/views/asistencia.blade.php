<!DOCTYPE html>
<html>
  <head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Asistencia</title>
  </head>
  <body >
    <div class="container">
                <h1 class="h1 text-center alert alert-info">Escaneo de Asistencia</h1>

                    <div class="text-center">
                        <video style="width: 100%; height: 90%;" id="preview" class="text-center"></video>
                    </div>

                <div style="display: none" id="result" class="mt-3 fs-5"></div>
                <button class="btn btn-info" onclick="finalizarAsistencia()">Finalizar Asistencia</button>
            <div style="padding: 10px" class="col-lg-4">
              <div style="display: none">
                <h2 class="fs-4">Personas Escaneadas</h2>
                <ul id="listaAsistencia" class="fs-5"></ul>
              </div>

                <h1 class="fs-4 alert alert-info">Personas Validadas:   <span  id="contadorPersonas">0</span></h1>

                <h3 id="validadosList" class="text-center"></h3>

                <div class="form-group text-center" style="padding: 20px">
                    <label for="tipoAsistencia" class="fs-4 alert alert-primary text-center">Tipo de Asistencia</label>
                    <select id="tipoAsistencia" class="form-control fs-5">
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </select>
                    {{-- <img src="http://www.poscoargentina.com/public/images/xlogo-posco-argentina-1.png.pagespeed.ic.HsKLPImI1W.webp" style="width: 70px" alt="" class="imagen-personal"> --}}
                </div>
            </div>
    </div>


    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

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
           scanner.start(cameras[2]);
        }

        else {
           scanner.start(cameras[0]);
        }
      }).catch(function (e) {
        console.error(e);
      });

        // Obtener las cámaras disponibles
//         Instascan.Camera.getCameras().then(function (cameras) {
//   if (cameras.length > 0) {
//     for (var i = 1; i <= 2; i++) {
//       try {
//         scanner.start(cameras[i]);
//         console.log("Validación exitosa en cámara " + i);
//         break;
//       } catch (error) {
//         console.error("Error en la validación de cámara " + i + ": " + error);
//       }
//     }
//   } else {
//     console.error("No se encontraron cámaras disponibles.");
//   }
// }).catch(function (e) {
//   console.error(e);
// });





      function finalizarAsistencia() {
  // Obtener el valor del estado seleccionado
  const estado = estadoSelect.value;

  // Mostrar los datos de asistencia en la consola antes de enviarlos
  console.log('Datos de Asistencia:', asistenciaData);

  // Enviar los datos de asistencia al servidor o realizar otras acciones
  axios.post('/guardar-asis', { asistencia: asistenciaData })
    .then(function (response) {
      console.log('Asistencia guardada exitosamente.', response.data);
      // Restablecer la lista de códigos coincidentes y la lista de personas validadas
      codigosCoincidentes.clear();
      validadosList.innerHTML = '';
      previousPageURL = document.referrer;
      window.location.href = previousPageURL;
    })
    .catch(function (error) {
      console.error('Error al guardar la asistencia:', error);
    });
}


const asistenciaData = [];

const codigosArray = [];

function validarCodigo(text) {
  if (codigosCoincidentes.has(text)) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: `Persona ya validada`,
      timer: 1000,
      showConfirmButton: false,
    });

    console.log("Código ya escaneado.");
    return;
  }

    // Captura la hora en este punto
    const fechaHora = new Date();
  const options = { timeZone: 'America/Argentina/Buenos_Aires', hour12: false };
  const fechaHoraArgentina = fechaHora.toLocaleString('es-AR', options).split(', ');
  const fecha = fechaHoraArgentina[0];
  const horaMinutoSegundo = fechaHoraArgentina[1].split(':');
  const hora = horaMinutoSegundo[0];
  const minuto = horaMinutoSegundo[1];
  const segundo = fechaHora.getSeconds();
  codigosArray.push(text);


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

        // Mostrar la alerta de SweetAlert
        Swal.fire({
          icon: 'success',
          title: 'Validado',
          text: `Persona validada: ${persona.nombre}`,
          timer: 1000,
          showConfirmButton: false,
        });

        // Agregar el código a la lista de asistencia
        asistencia.add(text);
        codigosCoincidentes.add(text);

        const contadorPersonas = document.getElementById('contadorPersonas');
        contadorPersonas.textContent = codigosCoincidentes.size;
        asistenciaData.push({
          codigo: text,
          fecha,
          hora: `${hora}:${minuto}:${segundo}`,
          estado : estadoSelect.value,
          timestamp: new Date().toISOString(), // Marca de tiempo cuando se escanea el código
        });

        console.log('Datos de Asistencia:', asistenciaData);


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
     // Función para mostrar un código validado en la lista de personas validadas
function mostrarValidado(nroIdentificacion, nombre) {
  const validadosList = document.getElementById('validadosList');

  // Borra todos los elementos de la lista
  validadosList.innerHTML = '';

  const listItem = document.createElement('h2');
  listItem.classList.add('alert', 'alert-success');

  listItem.textContent = `${nroIdentificacion} - ${nombre} (Validado)`;
  validadosList.appendChild(listItem);
}


      // Función para actualizar la lista de asistencia en la interfaz
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
