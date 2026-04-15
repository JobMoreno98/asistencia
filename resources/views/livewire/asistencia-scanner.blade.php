<div class="p-5">
    <form wire:submit.prevent="registrar">
        <input 
            type="text" 
            id="input-escaneo" 
            wire:model="codigo"
            class="w-full p-4 text-3xl border-2 border-gray-300 rounded focus:border-blue-500 outline-none"
            placeholder="Esperando escaneo..." 
            autofocus 
            autocomplete="off"
        >
        {{-- Botón invisible para asegurar el evento submit con el Enter del escáner --}}
        <button type="submit" class="hidden"></button>
    </form>

    <div class="mt-4 text-2xl">
        @if (session()->has('message'))
            <div class="text-green-600 font-bold">{{ session('message') }}</div>
        @endif

        @if (session()->has('error'))
            <div class="text-red-600 font-bold">{{ session('error') }}</div>
        @endif
    </div>

    <div wire:loading class="text-blue-500 animate-pulse">
        Registrando...
    </div>

    <script>
        // Foco automático infinito
        setInterval(() => {
            const el = document.getElementById('input-escaneo');
            if (document.activeElement !== el) el.focus();
        }, 100);
    </script>
</div>