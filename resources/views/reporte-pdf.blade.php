<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia</title>

    <style>
        @page {
            margin: 10px;
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

        /* LAYOUT */

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
            padding:2px 0;
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

        /* COLORS */

        .label-blue {
            color: #2563eb;
            font-weight: bold;
        }

        .label-green {
            color: #059669;
            font-weight: bold;
        }

        .label-red {
            color: #dc2626;
            font-weight: bold;
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
    </style>
</head>

<body>

    <h1>
        Reporte de Asistencia Ciclo {{ $ciclo }}
    </h1>

    <p>
        It is a long established fact that a reader will be distracted by the readable content of a page when looking at
        its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as
        opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing
        packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum'
        will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by
        accident, sometimes on purpose (injected humour and the like).
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
                            Registrados
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
                            Asistidos
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
                            Faltantes
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
                        Asistidos
                    </div>

                    <div class="summary-value">
                        {{ $totalRegistrados }}
                    </div>

                    <div class="summary-percent">
                        {{ round(($totalRegistrados * 100) / max($totalEsperados->count(), 1), 2) }}%
                    </div>

                </div>

                <div class="summary-box">

                    <div class="summary-label">
                        Total Esperados
                    </div>

                    <div class="summary-value">
                        {{ $totalEsperados->count() }}
                    </div>

                    <div class="summary-percent">
                        100%
                    </div>

                </div>

                <div class="summary-box">

                    <div class="summary-label">
                        Faltantes
                    </div>

                    <div class="summary-value">
                        {{ $totalEsperados->count() - $totalRegistrados }}
                    </div>

                    <div class="summary-percent">
                        {{ round((($totalEsperados->count() - $totalRegistrados) * 100) / max($totalEsperados->count(), 1), 2) }}%
                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
