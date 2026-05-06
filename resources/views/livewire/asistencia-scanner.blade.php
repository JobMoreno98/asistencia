<div class="p-6 max-w-lg mx-auto bg-white shadow-xl rounded-2xl border border-gray-200 font-sans">
    <!-- El input siempre debe tener el foco -->
    <input type="text" id="lector" 
        class="w-full p-4 text-2xl text-center border-2 border-blue-100 rounded-xl focus:border-blue-600 outline-none transition-all shadow-inner bg-gray-50"
        placeholder="ESCANEAR..." autofocus autocomplete="off">

    <!-- Pantalla de feedback -->
    <div id="feedback-pantalla" class="mt-8 min-h-[50px] flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-100 bg-gray-50 p-4 transition-colors">
        <p class="text-gray-400 uppercase text-xs font-bold tracking-widest">Esperando escaneo...</p>
    </div>

    <div class="mt-6 flex justify-between text-[10px] text-gray-400 font-bold uppercase italic">
        <span>Modo: Offline-Ready</span>
        <span>Pendientes: <span id="pendientes-num">0</span></span>
    </div>

    <script>
        const inputLector = document.getElementById('lector');
        const pantalla = document.getElementById('feedback-pantalla');
        const bolita = document.getElementById('status-bolita');
        const pendientesTxt = document.getElementById('pendientes-num');

        // 1. Cargamos los 3k registros a un Mapa de JS (ultra rápido)
        const rawData = @json($dbLocal);
        const personasMap = new Map(rawData.map(p => [p.codigo, p]));

        // Listener del escáner
        inputLector.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const val = inputLector.value.trim();
                inputLector.value = '';
                if (val) validarOffline(val);
            }
        });

        function validarOffline(codigo) {
            const persona = personasMap.get(codigo);

            if (persona) {
                // BIEN: Existe en la DB local
                mostrarFeedback(`✅ REGISTRADO<br><span class="text-2xl">${persona.nombre}</span>`, 'bg-green-500 text-white border-green-600');
                guardarEnLocalStorage(persona);
            } else {
                // ERROR: No existe
                mostrarFeedback(`❌ ERROR<br><span class="text-lg">Código no encontrado</span>`, 'bg-red-600 text-white border-red-700');
            }
        }

        function mostrarFeedback(html, clases) {
            pantalla.innerHTML = `<div class="text-center font-bold">${html}</div>`;
            pantalla.className = `mt-8 min-h-[150px] flex flex-col items-center justify-center rounded-2xl border-b-8 p-4 shadow-lg ${clases}`;
            
            // Regresar al estado neutro tras 3 segundos
            setTimeout(() => {
                pantalla.className = "mt-8 min-h-[150px] flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-100 bg-gray-50 p-4 transition-colors";
                pantalla.innerHTML = '<p class="text-gray-400 uppercase text-xs font-bold tracking-widest">Esperando escaneo...</p>';
            }, 3000);
        }

        function guardarEnLocalStorage(p) {
            let cola = JSON.parse(localStorage.getItem('asistencias_cola') || '[]');
            
            // Evitar duplicado local instantáneo (misma persona en la cola)
            if (cola.some(a => a.codigo === p.codigo)) return;

            cola.push({
                codigo: p.codigo,
                genero: p.genero,
                aula: p.aula,
                fecha: new Date().toISOString().slice(0, 19).replace('T', ' ')
            });

            localStorage.setItem('asistencias_cola', JSON.stringify(cola));
            actualizarContador();
            intentarSincronizar();
        }

        function intentarSincronizar() {
            let cola = JSON.parse(localStorage.getItem('asistencias_cola') || '[]');
            if (cola.length === 0) return;

            bolita.className = "w-3 h-3 rounded-full bg-yellow-400 animate-pulse";

            // Llamada a Livewire
            @this.sincronizarMasivo(cola).then(success => {
                if (success) {
                    localStorage.setItem('asistencias_cola', '[]');
                    bolita.className = "w-3 h-3 rounded-full bg-green-500";
                    actualizarContador();
                }
            }).catch(e => {
                bolita.className = "w-3 h-3 rounded-full bg-red-600";
            });
        }

        function actualizarContador() {
            let cola = JSON.parse(localStorage.getItem('asistencias_cola') || '[]');
            pendientesTxt.innerText = cola.length;
        }

        // Foco automático cada segundo
        setInterval(() => { if (document.activeElement !== inputLector) inputLector.focus(); }, 1000);
        
        // Sincronizar automáticamente cada 30 segundos
        setInterval(intentarSincronizar, 30000);
    </script>
</div>