@php
    $img = asset('img/logo_nuevo.png');
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia</title>

    <style>
        @page {
            margin: 5px 40px;

        }

        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 400;
            src: url('{{ asset('fonts/Montserrat-Regular.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 700;
            src: url('{{ asset('fonts/Montserrat-Bold.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'Times New Roman';
            font-style: normal;
            font-weight: 700;
            src: url('{{ asset('fonts/Times New Roman Bold.ttf') }}') format('truetype');
        }

        main {
            margin-bottom: 40px !important;

        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            text-transform: uppercase;
            font-size: 18px;
            margin-bottom: 10px;
            color: #111827;
        }

        p {
            margin-bottom: 20px;
            line-height: 1.5;
            text-align: justify;
        }

        .container {
            width: 100%;
        }

        .left-column {
            width: 68%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
        }

        .right-column {
            width: 28%;
            display: inline-block;
            vertical-align: top;
        }

        /* CARDS */

        .card {
            background: #ffffff;
            border: 1px solid #d1d5db;
            padding: 10px;
            margin-bottom: 18px;
        }

        .card-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a8a;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        /* HEADERS */

        .row-header {
            background: #1e40af;
            color: white;
            padding: 8px 0;
            font-size: 11px;
            font-weight: bold;
        }

        .row-header div {
            display: inline-block;
            vertical-align: top;
        }

        /* ROWS */

        .data-row {
            border-bottom: 1px solid #e5e7eb;
            padding: 2px 0;
        }

        .data-row div {
            display: inline-block;
            vertical-align: top;
        }

        .col-label {
            width: 48%;
            padding-left: 10px;
        }

        .col-value {
            width: 22%;
            text-align: center;
        }

        .col-percent {
            width: 22%;
            text-align: center;
        }

        /* SUMMARY */

        .summary-box {
            border: 1px solid #d1d5db;
            background: #f9fafb;
            padding: 10px;
            margin-bottom: 12px;
        }

        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 4px;
        }

        .summary-percent {
            font-size: 12px;
            color: #4b5563;
        }

        #header {
            position: fixed;
            top: 0px;
            left: 0px;
            right: 0px;
            height: 50px;
            line-height: 35px;
            font-size: 17px;
        }
    </style>
</head>

<body>
    <header id="header">
        <div style="height: 100%">
            <img src="{{ $img }}" height="140px" style="float:left;margin-right:10px; padding-right:10px;">
            <p style="margin-top: 30px;line-height:.8;">
                <span class="bold-text titulo">UNIVERSIDAD DE GUADALAJARA </span><br>
                <span style="color:#7D91BE;font-size: 10pt;" class="bold-text"> CENTRO UNIVERSITARIO DE CIENCIAS
                    SOCIALES Y HUMANIDADES</span> <br>
                <span style="font-size: 8pt;">SECRETARÍA ADMINISTRATIVA</span> <br>
                <span style="font-size: 8pt;">COORDINACIÓN DE CONTROL ESCOLAR</span>
            </p>
        </div>
    </header>
    <main style="clear: both;">

        <p>
            El Centro Universitario de Ciencias Sociales y Humanidades, a través de la Secretaría Administrativa y la
            Coordinación de Control Escolar, informa que en la Prueba de Aptitud Académica correspondiente al Ciclo
            Escolar
            2026-B se contó con la siguiente asistencia de aspirantes:
        </p>

        <div class="container">

            <!-- LEFT -->
            <div class="left-column">

                @php
                    $genero = [
                        'M' => 'Masculino',
                        'F' => 'Femenino',
                        'X' => 'Otro',
                    ];
                @endphp

                @foreach (['M', 'F', 'X'] as $g)
                    @php
                        $dato = $porGenero->firstWhere('genero', $g)->total ?? 0;
                        $registrados = $totalEsperados->where('genero', $g)->count();
                        $faltante = $registrados - $dato;
                    @endphp

                    <div class="card">

                        <div class="card-title">
                            {{ $genero[$g] }}
                        </div>

                        <!-- HEADER -->
                        <div class="row-header">

                            <div class="col-label">
                                Estado
                            </div>

                            <div class="col-value">
                                Cantidad
                            </div>

                            <div class="col-percent">
                                %
                            </div>

                        </div>

                        <!-- REGISTRADOS -->
                        <div class="data-row">

                            <div class="col-label label-blue">
                                Aspirantes citados
                            </div>

                            <div class="col-value">
                                {{ $registrados }}
                            </div>

                            <div class="col-percent">
                                {{ round(($registrados * 100) / max($totalEsperados->count(), 1), 2) }}%
                            </div>

                        </div>

                        <!-- ASISTIDOS -->
                        <div class="data-row">

                            <div class="col-label label-green">
                                Asistentes
                            </div>

                            <div class="col-value">
                                {{ $dato }}
                            </div>

                            <div class="col-percent">
                                {{ round(($dato * 100) / max($registrados, 1), 2) }}%
                            </div>

                        </div>

                        <!-- FALTANTES -->
                        <div class="data-row">

                            <div class="col-label label-red">
                                Ausentes
                            </div>

                            <div class="col-value">
                                {{ $faltante }}
                            </div>

                            <div class="col-percent">
                                {{ round(($faltante * 100) / max($registrados, 1), 2) }}%
                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

            <!-- RIGHT -->
            <div class="right-column">

                <div class="card">

                    <div class="card-title">
                        Totales Generales
                    </div>

                    <div class="summary-box">

                        <div class="summary-label">
                            Asistentes
                        </div>

                        <div class="summary-value">
                            {{ $totalRegistrados }}
                            |
                            {{ round(($totalRegistrados * 100) / max($totalEsperados->count(), 1), 2) }}%
                        </div>

                    </div>

                    <div class="summary-box">

                        <div class="summary-label">
                            Aspirantes citados
                        </div>

                        <div class="summary-value">
                            {{ $totalEsperados->count() }}
                            |
                            100%
                        </div>

                    </div>

                    <div class="summary-box">

                        <div class="summary-label">
                            Ausentes
                        </div>

                        <div class="summary-value">
                            {{ $totalEsperados->count() - $totalRegistrados }} |
                            {{ round((($totalEsperados->count() - $totalRegistrados) * 100) / max($totalEsperados->count(), 1), 2) }}%
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

</body>

</html>
