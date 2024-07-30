<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
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
  </style>
</head>
<body>
  <div class="container">
    <h1 class="h1 text-center alert alert-info">Escaneo de Asistencia</h1>
    <div id="reader-container" class="position-relative">
      <button id="camera-switch-btn" class="btn btn-primary">
        <i class="bi bi-camera"></i> Cambiar cámara
      </button>
      <div id="reader" class="text-center"></div>
    </div>
    <div style="display: none" id="result" class="mt-3 fs-5"></div>
    <button class="btn btn-info mt-3" onclick="finalizarAsistencia()">Finalizar Asistencia</button>
    <div style="padding: 10px" class="col-lg-4">
      <h1 class="fs-4 alert alert-info">Personas Validadas: <span id="contadorPersonas">0</span></h1>
      <h3 id="validadosList" class="text-center"></h3>
      <div class="form-group text-center" style="padding: 20px">
        <label for="tipoAsistencia" class="fs-4 alert alert-primary text-center">Tipo de Asistencia</label>
        <select id="tipoAsistencia" class="form-control fs-5">
          <option value="entrada">Entrada</option>
          <option value="salida">Salida</option>
        </select>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
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
        fps: 2,  // Reducido a 2 FPS para escanear más lento
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
      };

      html5QrCode.start(
        { facingMode: currentCamera },
        config,
        onScanSuccess
      ).catch((err) => {
        console.error("Error al iniciar el escáner:", err);
      });
    }

    function onScanSuccess(decodedText, decodedResult) {
      if (/^\d+$/.test(decodedText)) {
        validarCodigo(decodedText);
      } else {
        mostrarError("Código inválido: no es un número.");
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
      axios.post('/guardar-asis', { asistencia: asistenciaData })
        .then(function (response) {
          console.log('Asistencia guardada exitosamente.', response.data);
          codigosCoincidentes.clear();
          document.getElementById('validadosList').innerHTML = '';
          previousPageURL = document.referrer;
          window.location.href = previousPageURL;
        })
        .catch(function (error) {
          console.error('Error al guardar la asistencia:', error);
        });
    }

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

      const fechaHora = new Date();
      const options = { timeZone: 'America/Argentina/Buenos_Aires', hour12: false };
      const fechaHoraArgentina = fechaHora.toLocaleString('es-AR', options).split(', ');
      const fecha = fechaHoraArgentina[0];
      const horaMinutoSegundo = fechaHoraArgentina[1].split(':');
      const hora = horaMinutoSegundo[0];
      const minuto = horaMinutoSegundo[1];
      const segundo = fechaHora.getSeconds();

      axios.post('/buscar-coincidencias', { codigo: text })
        .then(function (response) {
          console.log(response.data);
          const coincidencias = response.data.coincidencias;
          if (coincidencias.length === 0) {
            mostrarError("Código no encontrado en la base de datos.");
          } else {
            const persona = coincidencias[0];
            mostrarValidado(persona.nro_identificacion, persona.nombre);
            Swal.fire({
              icon: 'success',
              title: 'Validado',
              text: `Persona validada: ${persona.nombre}`,
              timer: 1000,
              showConfirmButton: false,
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
        .catch(function (error) {
          console.error(error);
          mostrarError("Error al buscar coincidencias en la base de datos.");
        });
    }

    function mostrarError(message) {
      const resultElement = document.getElementById('result');
      resultElement.innerHTML = `<p>Error: ${message}</p>`;
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