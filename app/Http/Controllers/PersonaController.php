<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Persona;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function pdf($ciclo)
    {
        $porGenero = Asistencia::select('genero', DB::raw('count(*) as total'))->groupBy('genero')->get();

        $reporteAulas = DB::table('personas')
            ->select('aula', DB::raw('count(*) as esperados'), DB::raw('(SELECT count(*) FROM asistencias WHERE asistencias.aula = personas.aula) as registrados'))
            ->where('ciclo', $ciclo)->groupBy('aula')->orderBy('registrados', 'desc')->get();

        $totalEsperados = Persona::with('asistencias')->where('ciclo', $ciclo)->get();

        $totalRegistrados = $totalEsperados->filter(function ($persona) {
            return $persona->asistencias->isNotEmpty();
        })->count();


        $html = view('reporte-pdf', compact('totalEsperados', 'porGenero','totalRegistrados','ciclo'));

        //return $html;

        $pdf = Pdf::loadHtml($html->render())
            ->setPaper('letter', 'landscape')
            ->setOptions([
                'defaultFont' => 'Montserrat',
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        $pdf->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->get_canvas();
        $width = $canvas->get_width();
        $x_center = $width / 2 - 50;

        $canvas->page_text($x_center, 750, 'Parres Arias No. 150 Los Belenes C.P. 45132.', null, 8, [0, 0, 0]);
        $canvas->page_text(100, 760, 'www.cucsh.udg.mx', null, 11, '#7D91BE');
        $canvas->page_text($x_center, 760, 'Zapopan, Jalisco, México.   Tel. +52 (33) 38193300 Ext. 23409', null, 8, [0, 0, 0]);
        $canvas->page_text($x_center, 770, 'Página {PAGE_NUM} de {PAGE_COUNT}', null, 8, [0, 0, 0]);

        return $pdf->stream();

        return view('reporte-pdf', compact('totalEsperados', 'porGenero'));
    }
}
