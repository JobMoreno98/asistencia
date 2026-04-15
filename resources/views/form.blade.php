<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <form wire:submit.prevent="registrar">
        <input type="text" wire:model="codigo" autofocus placeholder="Escanee el código de barras aquí..."
            class="w-full p-4 text-2xl border-2 border-blue-500 rounded focus:outline-none">

        <div wire:loading wire:target="registrar" class="mt-2 text-blue-600">
            Registrando en la base de datos...
        </div>
    </form>

</body>

</html>
