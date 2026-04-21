<div class="bg-gray-100 min-h-screen p-8 font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="border-b-2 border-blue-900 mb-8 pb-4">
            <h1 class="text-3xl font-light text-gray-800 uppercase tracking-wide">Control de Asistencia
            </h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 items-center">
            @php
                $genero = ['M' => 'Masculino', 'F' => 'Femenino', 'X' => 'Otro'];
            @endphp
            @foreach (['M', 'F', 'X'] as $g)
                @php
                    $dato = $porGenero->firstWhere('genero', $g)->total ?? 0;
                    //dd($dato, $totalRegistrados->where('genero', $g)->count());
                @endphp
                <div class="bg-white border-l-4 border-blue-800 shadow-sm p-6">
                    <dt class="text-md font-medium text-gray-500 uppercase tracking-wider">{{ $g }}</dt>
                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full border border-blue-900 rounded-lg shadow-lg">
                            <thead>
                                <tr class="bg-blue-800 text-white">
                                    <th class="px-6 py-3 text-left text-sm font-semibold"></th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold">Cantidad</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold">%</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-blue-900">
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-900 font-medium">Registrados</td>
                                    <td class="px-2 py-2 text-center text-gray-700">
                                        {{ $totalEsperados->where('genero', $g)->count() }}
                                    </td>
                                    <td class="px-2 py-2 text-center text-gray-700">
                                        {{ round(($totalEsperados->where('genero', $g)->count() * 100) / $totalEsperados->count(), 2) }}
                                        %
                                    </td>
                                </tr>
                                <tr class="hover:bg-green-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-900 font-medium">Asistidos</td>
                                    <td class="px-2 py-2 text-center text-gray-700">{{ $dato }}</td>
                                    <td class="px-2 py-2 text-center text-gray-700">
                                        {{ round(($dato * 100) / $totalEsperados->where('genero', $g)->count(), 2) }} %
                                    </td>
                                </tr>
                                <tr class="hover:bg-red-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-900 font-medium">Faltantes</td>
                                    <td class="px-2 py-2 text-center text-gray-700">
                                        @php
                                            $faltante = $totalEsperados->where('genero', $g)->count() - $dato;
                                        @endphp
                                        {{ $faltante }}
                                    </td>
                                    <td class="px-2 py-2 text-center text-gray-700">
                                        {{ round(($faltante * 100) / $totalEsperados->where('genero', $g)->count(), 2) }}
                                        %
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            @endforeach
            <div class="bg-white border-l-4 border-blue-800 shadow-sm p-6">
                <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Registrados</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalRegistrados->count() }} |
                    {{ round(($totalRegistrados->count() * 100) / $totalEsperados->count(), 2) }} %</dd>
            </div>
            <div class="bg-white border-l-4 border-blue-800 shadow-sm p-6">
                <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Esperados</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalEsperados->count() }} | 100%</dd>
            </div>
        </div>

        <div class="bg-white shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">Resumen de Asistencia por Aula</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aula</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Esperados</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Registrados</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Porcentaje de Asistencia</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($reporteAulas as $aula)
                            @php
                                $porcentaje = $aula->esperados > 0 ? ($aula->registrados / $aula->esperados) * 100 : 0;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $aula->aula }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600">
                                    {{ $aula->esperados }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold text-blue-800">
                                    {{ $aula->registrados }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2 max-w-[100px] mr-3">
                                            <div class="bg-blue-900 h-2 rounded-full"
                                                style="width: {{ min($porcentaje, 100) }}%"></div>
                                        </div>
                                        <span
                                            class="text-xs font-medium text-gray-700">{{ number_format($porcentaje, 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 flex justify-between items-center text-xs text-gray-400 uppercase tracking-widest">
            <span>Generado: {{ now()->format('d/m/Y H:i') }}</span>
            <button wire:click="$refresh"
                class="border border-gray-300 px-4 py-2 hover:bg-gray-200 transition">Actualizar Datos</button>
        </div>
    </div>
</div>
