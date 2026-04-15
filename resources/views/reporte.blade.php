<div class="grid grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="font-bold text-lg mb-4 text-gray-700">Asistencia por Género</h3>
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Género</th>
                    <th class="py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($porGenero as $item)
                    <tr class="border-b">
                        <td class="py-2 capitalize">{{ $item->genero }}</td>
                        <td class="py-2 text-right font-bold">{{ $item->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="font-bold text-lg mb-4 text-gray-700">Asistencia por Aula</h3>
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Aula</th>
                    <th class="py-2 text-right">Asistentes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($porAula as $item)
                    <tr class="border-b">
                        <td class="py-2">{{ $item->aula }}</td>
                        <td class="py-2 text-right font-bold">{{ $item->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
