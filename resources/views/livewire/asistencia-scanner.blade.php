<div class="p-6 max-w-lg mx-auto bg-white shadow-xl rounded-2xl border border-gray-200 font-sans">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-blue-900 font-black tracking-tighter text-xl">ASISTENCIA</h2>
        <!-- Asegúrate de que este ID exista para la bolita de estado -->
        <div id="status-bolita" class="w-3 h-3 rounded-full bg-gray-400"></div>
    </div>

    <input type="text" id="lector"
        class="w-full p-4 text-2xl text-center border-2 border-blue-100 rounded-xl focus:border-blue-600 outline-none transition-all shadow-inner bg-gray-50"
        placeholder="ESCANEAR..." autofocus autocomplete="off">

    <div id="feedback-pantalla"
        class="mt-8 min-h-[50px] flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-100 bg-gray-50 p-4 transition-colors">
        <p class="text-gray-400 uppercase text-xs font-bold tracking-widest">Esperando escaneo...</p>
    </div>

    <div class="mt-6 flex justify-between text-[10px] text-gray-400 font-bold uppercase italic">
        <span>Registrados: <span id="escaneados-num">0</span></span>
        <span>Pendientes: <span id="pendientes-num">0</span></span>

    </div>

    <!-- NUEVO BOTÓN DE SINCRONIZACIÓN MANUAL -->
    <button onclick="forzarSincronizacionTotal()"
        class="mt-8 w-full py-2 bg-gray-200 hover:bg-blue-900 hover:text-white text-gray-600 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Actualizar Base de Datos y Sincronizar
    </button>
    <button onclick="descargarCSV()"
        class="mt-8 w-full py-2 bg-blue-900 text-white text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all flex items-center justify-center gap-2">
        Descargar base de datos
    </button>

    <script>
        (function() {
            if (window.asistenciaIniciada) return;
            window.asistenciaIniciada = true;

            // 1. DECLARACIÓN DE VARIABLES (Usamos var para evitar errores de re-declaración)
            var inputLector = document.getElementById('lector');
            var pantalla = document.getElementById('feedback-pantalla');
            var bolita = document.getElementById('status-bolita');
            var pendientesTxt = document.getElementById('pendientes-num');
            var registrados = document.getElementById('registrados');

            // 2. CARGA DE DATOS
            var rawData = @json($dbLocal);
            var personasMap = new Map(rawData.map(p => [p.codigo, p]));
            var yaRegistradosServidor = @json($yaRegistrados);

            var yaRegistradosLocal = JSON.parse(localStorage.getItem('asistencias_duplicados') || '[]');

            var registradosHoy = new Set([...yaRegistradosServidor, ...yaRegistradosLocal]);

            localStorage.setItem('asistencias_duplicados', JSON.stringify(Array.from(registradosHoy)));
            window.forzarSincronizacionTotal = function() {
                localStorage.removeItem('asistencias_cola');
                localStorage.removeItem('asistencias_duplicados');
                var cola = JSON.parse(localStorage.getItem('asistencias_cola') || '[]');

                window.mostrarFeedback('PROCESANDO...', 'bg-green-700 text-white');

                if (cola.length > 0) {
                    @this.call('sincronizarMasivo', cola)
                        .then(function(success) {
                            if (success === true) {
                                localStorage.setItem('asistencias_cola', '[]');

                                window.actualizarContador();

                                window.mostrarFeedback('SINCRONIZADO', 'bg-green-600 text-white', false);

                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500);

                            } else {
                                window.mostrarFeedback('ERROR EN SERVIDOR', 'bg-red-700 text-white');
                            }
                        })
                        .catch(function(error) {
                            console.error(error);
                            window.mostrarFeedback('SIN CONEXIÓN', 'bg-red-800 text-white');
                        });
                } else {
                    window.mostrarFeedback('ACTUALIZANDO BD...', 'bg-gray-600 text-white');
                    setTimeout(function() {
                        window.location.reload();
                    }, 5000);
                }
                window.actualizarEscaneados();
            };

            window.validarOffline = function(codigo) {
                var persona = personasMap.get(codigo);

                if (!persona) {
                    window.mostrarFeedback(`ERROR<br><span class="text-md">Código no encontrado</span>`,
                        'bg-red-600 text-white ');
                    return;
                }

                if (registradosHoy.has(codigo)) {
                    window.mostrarFeedback(
                        `DUPLICADO<br><span class="text-md">${persona.nombre} ya registró su entrada</span>`,
                        'bg-yellow-500 text-white ');
                    return;
                }

                window.mostrarFeedback(
                    `REGISTRADO<br><span class="text-md">${persona.nombre}</span>`,
                    'bg-green-500 text-white ',
                    8000
                );

                registradosHoy.add(codigo);
                localStorage.setItem('asistencias_duplicados', JSON.stringify(Array.from(registradosHoy)));

                window.actualizarEscaneados();

                window.guardarEnLocalStorage(persona);
            };

            let feedbackTimeout;
            window.mostrarFeedback = function(html, clases, duracion = 300) {
                clearTimeout(feedbackTimeout);
                pantalla.innerHTML = `<div class="text-center font-bold">${html}</div>`;
                pantalla.className = `... ${clases}`;
                feedbackTimeout = setTimeout(function() {
                    pantalla.className = "...";
                    pantalla.innerHTML = "...";
                }, duracion);
            };
            window.guardarEnLocalStorage = function(p) {
                var cola = JSON.parse(localStorage.getItem('asistencias_cola') || '[]');
                cola.push({
                    codigo: p.codigo,
                    genero: p.genero,
                    aula: p.aula,
                    fecha: new Date().toISOString().slice(0, 19).replace('T', ' ')
                });
                localStorage.setItem('asistencias_cola', JSON.stringify(cola));

                var backup = JSON.parse(
                    localStorage.getItem('asistencias_backup') || '[]'
                );

                backup.push(p);

                localStorage.setItem(
                    'asistencias_backup',
                    JSON.stringify(backup)
                );

                window.actualizarContador();
                window.intentarSincronizar();
            };

            window.intentarSincronizar = function() {
                var cola = JSON.parse(localStorage.getItem('asistencias_cola') || '[]');
                if (cola.length === 0 || !window.Livewire) return;

                bolita.className = "w-3 h-3 rounded-full bg-yellow-400 animate-pulse";

                @this.call('sincronizarMasivo', cola)
                    .then(success => {
                        if (success === true) {
                            localStorage.setItem('asistencias_cola', '[]');
                            bolita.className = "w-3 h-3 rounded-full bg-green-500";
                            window.actualizarContador();
                        } else {
                            bolita.className = "w-3 h-3 rounded-full bg-red-600";
                        }
                    })
                    .catch(error => {
                        bolita.className = "w-3 h-3 rounded-full bg-red-600";
                    });
            };

            window.actualizarContador = function() {
                var cola = JSON.parse(localStorage.getItem('asistencias_cola') || '[]');

                pendientesTxt.innerText = cola.length;
            };

            // 4. EVENTOS Y TIMERS
            inputLector.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    var val = inputLector.value.trim();
                    inputLector.value = '';
                    if (val) window.validarOffline(val);
                }
            });

            setInterval(function() {
                if (document.activeElement !== inputLector) inputLector.focus();
            }, 1000);

            setInterval(window.intentarSincronizar, 30000);

            // Ejecución inicial
            window.actualizarContador();
            window.intentarSincronizar();



            window.descargarCSV = function() {
                var cola = JSON.parse(localStorage.getItem('asistencias_backup') || '[]');

                if (cola.length === 0) {
                    alert('No hay datos');
                    return;
                }

                // Encabezados CSV
                var csv = 'codigo,nombre,genero\n';

                cola.forEach(function(item) {
                    // Buscar persona completa
                    //console.log(item, personasMap.get(item))
                    var persona = personasMap.get(item);
                    console.log(item.codigo)

                    var codigo = item.codigo || '';
                    var nombre = item?.nombre || '';
                    var genero = item.genero || '';

                    // Escapar comillas
                    nombre = `"${String(nombre).replace(/"/g, '""')}"`;

                    csv += `${codigo},${nombre},${genero}\n`;
                });

                // Crear archivo
                var blob = new Blob([csv], {
                    type: 'text/csv;charset=utf-8;'
                });

                // Descargar
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'asistencias.csv';

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            };

            window.actualizarEscaneados = function() {
                var registrados = JSON.parse(
                    localStorage.getItem('asistencias_backup') || '[]'
                );

                document.getElementById('escaneados-num').innerText = registrados.length;
            };
        })();
    </script>
</div>
