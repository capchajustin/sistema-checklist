<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivitySubmission;
use Carbon\Carbon;

class ChecklistController extends Controller
{
    /**
     * Arreglo maestro de bloques horarios oficiales.
     */
    private function getTimeBlocks()
    {
        return [
            '08:00 - 09:00', '09:00 - 10:00', '10:00 - 11:00',
            '11:00 - 12:00', '12:00 - 13:00', '13:00 - 14:00',
            '14:00 - 15:00', '15:00 - 16:00', '16:00 - 17:00',
            '17:00 - 18:00', '18:00 - 19:00', '19:00 - 20:00', // Expandido hasta las 8 PM
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $now = Carbon::now('America/Lima');
        $today = $now->toDateString();
        $currentTimeString = $now->format('H:i');

        $timeBlocks = $this->getTimeBlocks();

        // Detectar bloque seleccionado en la URL o asignar el primero por defecto
        $selectedBlock = $request->query('block', $timeBlocks[0]);
        if (!in_array($selectedBlock, $timeBlocks)) {
            $selectedBlock = $timeBlocks[0];
        }

        // Obtener bloques completados hoy por el usuario
        $completedBlocks = ActivitySubmission::where('user_id', $user->id)
            ->where('submitted_at', $today)
            ->pluck('time_block')
            ->toArray();

        // Obtener datos del bloque actual si ya existe entrega
        $currentSubmission = ActivitySubmission::where('user_id', $user->id)
            ->where('submitted_at', $today)
            ->where('time_block', $selectedBlock)
            ->first();

        // --- LÓGICA DE VALIDACIÓN HORARIA (FRONTEND) ---
        // Extraemos las horas de inicio y fin del bloque seleccionado (Ej: "08:00" y "09:00")
        list($startTime, $endTime) = explode(' - ', $selectedBlock);

        // Evaluamos el estado del bloque respecto a la hora de Lima
        $isTimeValid = ($currentTimeString >= $startTime && $currentTimeString < $endTime);
        $isPast = ($currentTimeString >= $endTime);

        return view('dashboard', compact(
            'timeBlocks', 
            'selectedBlock', 
            'completedBlocks', 
            'currentSubmission', 
            'isTimeValid',
            'isPast'
        ));
    }

    public function storeSubmission(Request $request)
    {
        $request->validate([
            'time_block'     => 'required|string',
            'project_name'   => 'required|string|max:255',
            'activity_title' => 'required|string|max:255',
            'description'    => 'required|string|max:2000',
            'evidence_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = auth()->user();
        $now = Carbon::now('America/Lima');
        $today = $now->toDateString();
        $currentTimeString = $now->format('H:i');

        // --- SEGURIDAD A NIVEL BACKEND ---
        // Validar que el bloque exista
        if (!in_array($request->time_block, $this->getTimeBlocks())) {
            return redirect()->back()->withErrors(['error' => 'Bloque horario no válido.']);
        }

        // Extraer horas de control
        list($startTime, $endTime) = explode(' - ', $request->time_block);

        // Si la hora actual no pertenece al rango, rebota la petición (anti-hackers de consola)
        if ($currentTimeString < $startTime || $currentTimeString >= $endTime) {
            return redirect()->back()->withErrors(['error' => 'No puedes saltarte las reglas de control. Este bloque está cerrado por horario.']);
        }

        // Guardar archivo multimedia
        $photoPath = null;
        if ($request->hasFile('evidence_photo')) {
            $photoPath = $request->file('evidence_photo')->store('evidences', 'public');
        }

        // Registrar entrega en la BD
        ActivitySubmission::create([
            'user_id'        => $user->id,
            'time_block'     => $request->time_block,
            'project_name'   => $request->project_name,
            'activity_title' => $request->activity_title,
            'description'    => $request->description,
            'evidence_photo' => $photoPath,
            'submitted_at'   => $today,
        ]);

        return redirect()->route('dashboard', ['block' => $request->time_block])->with('status', 'success');
    }
    public function submit(Request $request)
{
    $request->validate([
        'time_block'     => 'required|string',
        'project_name'   => 'required|string|max:255',
        'activity_title' => 'required|string|max:255',
        'description'    => 'required|string',
        'evidence_photo' => 'required|image|max:4096', // Máximo 4MB
    ]);

    // Procesar y guardar la imagen en el storage público
    $path = $request->file('evidence_photo')->store('evidences', 'public');

    // CREACIÓN DEL REGISTRO COMPLETO VINCULADO AL USUARIO
    ActivitySubmission::create([
        'user_id'        => auth()->id(), // <-- AQUÍ SE CAPTURA AL TRABAJADOR LOGUEADO
        'time_block'     => $request->time_block,
        'project_name'   => $request->project_name,
        'activity_title' => $request->activity_title,
        'description'    => $request->description,
        'evidence_photo' => $path,
        'status'         => 'pending', // Entra como pendiente de revisión automatica
        'submitted_at'   => now(),
    ]);

    return redirect()->route('dashboard')->with('status', 'success');
}
}