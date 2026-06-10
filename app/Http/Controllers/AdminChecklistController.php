<?php

namespace App\Http\Controllers;

use App\Models\ActivitySubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminChecklistController extends Controller
{
    public function index(Request $request)
    {
        // 1. Capturar los filtros enviados por el formulario (por defecto hoy y mes actual)
        $userId      = $request->input('user_id');
        $dateFilter  = $request->input('date', Carbon::today('America/Lima')->toDateString());
        $monthFilter = $request->input('month', Carbon::now('America/Lima')->format('Y-m'));

        // 2. Obtener la lista de todos los trabajadores para llenar el selector
        $users = User::orderBy('name', 'asc')->get(); 

        // Inicializar los acumuladores analíticos
        $dayHours    = 0;
        $monthHours  = 0;
        $submissions = collect();

        // 3. Si el administrador seleccionó a un trabajador, ejecutamos los cálculos
        if ($userId) {
            // Obtener todas las actividades del día seleccionado para mostrarlas en la línea de tiempo
            $submissions = ActivitySubmission::where('user_id', $userId)
                ->whereDate('created_at', $dateFilter)
                ->orderBy('time_block', 'asc')
                ->get();

            // CALCULAR HORAS DEL DÍA: Solo bloques aprobados ('approved')
            $approvedDaySubmissions = ActivitySubmission::where('user_id', $userId)
                ->whereDate('created_at', $dateFilter)
                ->where('status', 'approved')
                ->get();
            $dayHours = $this->calculateTimeBlocksToHours($approvedDaySubmissions);

            // CALCULAR HORAS DEL MES ACUMULADAS: Solo bloques aprobados ('approved')
            $parsedMonth = explode('-', $monthFilter);
            $approvedMonthSubmissions = ActivitySubmission::where('user_id', $userId)
                ->whereYear('created_at', $parsedMonth[0])
                ->whereMonth('created_at', $parsedMonth[1])
                ->where('status', 'approved')
                ->get();
            $monthHours = $this->calculateTimeBlocksToHours($approvedMonthSubmissions);
        }

        return view('admin.dashboard', compact(
            'users', 'submissions', 'dayHours', 'monthHours', 
            'userId', 'dateFilter', 'monthFilter'
        ));
    }

    /**
     * Procesa las cadenas de bloques horarias (Ej: "08:00 - 09:30") y las convierte a horas reales decimales.
     */
    private function calculateTimeBlocksToHours($submissions)
    {
        $totalMinutes = 0;

        foreach ($submissions as $submission) {
            if (str_contains($submission->time_block, ' - ')) {
                list($startTime, $endTime) = explode(' - ', $submission->time_block);
                
                // Convertir horas y minutos de inicio/fin a minutos enteros absolutos
                list($startH, $startM) = explode(':', $startTime);
                list($endH, $endM) = explode(':', $endTime);
                
                $startTotalMinutes = ($startH * 60) + $startM;
                $endTotalMinutes   = ($endH * 60) + $endM;

                if ($endTotalMinutes > $startTotalMinutes) {
                    $totalMinutes += ($endTotalMinutes - $startTotalMinutes);
                }
            }
        }

        // Retorna las horas con un formato decimal limpio (Ej: 4.5 horas)
        return round($totalMinutes / 60, 2);
    }

    /**
     * Procesa la actualización del estado (Apto / No Apto) desde la interfaz
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $submission = ActivitySubmission::findOrFail($id);
        $submission->status = $request->status;
        $submission->save();

        return back()->with('status_updated', 'El estado de la actividad fue actualizado correctamente.');
    }
}