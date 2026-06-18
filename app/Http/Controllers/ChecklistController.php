<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivitySubmission;
use Carbon\Carbon;
use Cloudinary\Configuration\Configuration; 
use Cloudinary\Api\Upload\UploadApi;      

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
            '17:00 - 18:00', '18:00 - 19:00', '19:00 - 20:00',
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $now = Carbon::now('America/Lima');
        $today = $now->toDateString();
        $currentTimeString = $now->format('H:i');

        $timeBlocks = $this->getTimeBlocks();

        $selectedBlock = $request->query('block', $timeBlocks[0]);
        if (!in_array($selectedBlock, $timeBlocks)) {
            $selectedBlock = $timeBlocks[0];
        }

        $completedBlocks = ActivitySubmission::where('user_id', $user->id)
            ->where('submitted_at', $today)
            ->pluck('time_block')
            ->toArray();

        $currentSubmission = ActivitySubmission::where('user_id', $user->id)
            ->where('submitted_at', $today)
            ->where('time_block', $selectedBlock)
            ->first();

        list($startTime, $endTime) = explode(' - ', $selectedBlock);

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

    /**
     * Método para almacenar usando el SDK Nativo de Cloudinary
     */
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
        if (!in_array($request->time_block, $this->getTimeBlocks())) {
            return redirect()->back()->withErrors(['error' => 'Bloque horario no válido.']);
        }

        list($startTime, $endTime) = explode(' - ', $request->time_block);

        if ($currentTimeString < $startTime || $currentTimeString >= $endTime) {
            return redirect()->back()->withErrors(['error' => 'No puedes saltarte las reglas de control. Este bloque está cerrado por horario.']);
        }

        // --- SUBIDA USANDO EL SDK NATIVO ---
        $photoUrl = null;
        if ($request->hasFile('evidence_photo')) {
            // Inicializar la configuración leyendo directamente de la variable de Render
            Configuration::instance(env('CLOUDINARY_URL'));

            // Ejecutar la subida directa
            $uploadApi = new UploadApi();
            $response = $uploadApi->upload($request->file('evidence_photo')->getRealPath(), [
                'folder' => 'evidencias_checklist'
            ]);

            // Extraer la URL segura devuelta por la API
            $photoUrl = $response['secure_url'];
        }

        // --- CREACIÓN DEL REGISTRO EN LA BD DE AIVEN ---
        ActivitySubmission::create([
            'user_id'        => $user->id,
            'time_block'     => $request->time_block,
            'project_name'   => $request->project_name,
            'activity_title' => $request->activity_title,
            'description'    => $request->description,
            'evidence_photo' => $photoUrl, 
            'status'         => 'pending',
            'submitted_at'   => $today,
        ]);

        return redirect()->route('dashboard', ['block' => $request->time_block])->with('status', 'success');
    }
}