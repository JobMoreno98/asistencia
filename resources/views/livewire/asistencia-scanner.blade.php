<div class="p-6 max-w-lg mx-auto bg-white shadow-xl rounded-2xl border border-gray-200 font-sans">

    <input type="text" id="lector"
        class="w-full p-4 text-2xl text-center border-2 border-blue-100 rounded-xl focus:border-blue-600 outline-none transition-all shadow-inner bg-gray-50"
        placeholder="ESCANEAR..." autofocus autocomplete="off">

    <div id="feedback-pantalla"
        class="mt-8 min-h-[150px] flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-100 bg-gray-50 p-4 transition-colors">
        <p class="text-gray-400 uppercase text-xs font-bold tracking-widest">Esperando escaneo...</p>
    </div>

    <div class="mt-6 flex justify-between text-[10px] text-gray-400 font-bold uppercase italic">
        <span>Modo: Offline-Ready</span>
        <span>Pendientes: <span id="pendientes-num">0</span></span>
    </div>

    <script>
        (function() {
            if (window.asistenciaIniciada) return;
            window.asistenciaIniciada = true;

            // 1. DECLARACIÓN DE VARIABLES (Usamos var para evitar errores de re-declaración)
            var inputLector = document.getElementById('lector');
            var pantalla = document.getElementById('feedback-pantalla');
            var bolita = document.getElementById('status-bolita');
            var pendientesTxt = document.getElementById('pendientes-num');

            // 2. CARGA DE DATOS
            var rawData = @json($dbLocal);
            var personasMap = new Map(rawData.map(p => [p.codigo, p]));
            var registradosHoy = new Set(JSON.parse(localStorage.getItem('asistencias_duplicados') || '[]'));

            // 3. FUNCIONES DE LÓGICA
            window.validarOffline = function(codigo) {
                var persona = personasMap.get(codigo);

                if (!persona) {
                    window.mostrarFeedback(`❌ ERROR<br><span class="text-lg">Código no encontrado</span>`, 'bg-red-600 text-white border-red-700');
                    return;
                }

                if (registradosHoy.has(codigo)) {
                    window.mostrarFeedback(`⚠️ DUPLICADO<br><span class="text-lg">${persona.nombre} ya registró su entrada</span>`, 'bg-yellow-500 text-white border-yellow-600');
                    return;
                }

                // Éxito
                window.mostrarFeedback(`✅ REGISTRADO<br><span class="text-2xl">${persona.nombre}</span>`, 'bg-green-500 text-white border-green-600');
                
                registradosHoy.add(codigo);
                localStorage.setItem('asistencias_duplicados', JSON.stringify(Array.from(registradosHoy)));
                window.guardarEnLocalStorage(persona);
            };

            window.mostrarFeedback = function(html, clases) {
                pantalla.innerHTML = `<div class="text-center font-bold">${html}</div>`;
                pantalla.className = `mt-8 min-h-[150px] flex flex-col items-center justify-center rounded-2xl border-b-8 p-4 shadow-lg ${clases}`;

                setTimeout(function() {
                    pantalla.className = "mt-8 min-h-[150px] flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-100 bg-gray-50 p-4 transition-colors";
                    pantalla.innerHTML = '<p class="text-gray-400 uppercase text-xs font-bold tracking-widest">Esperando escaneo...</p>';
                }, 3500);
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
        })();
    </script>
</div>