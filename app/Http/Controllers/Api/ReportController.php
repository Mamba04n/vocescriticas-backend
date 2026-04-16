<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    /**
     * 4. Crear un reporte de un modelo polimórfico (Post o Comment, o incluso un User). 
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string|in:App\Models\Post,App\Models\Comment',
            'reason' => 'required|string|max:100', // Ejemplo: "Spam", "Plagio", "Acoso".
            'notes' => 'nullable|string|max:1000'
        ]);

        $report = Report::create([
            'user_id' => $request->user()->id,
            'reportable_id' => $validated['reportable_id'],
            'reportable_type' => $validated['reportable_type'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending' // Por defecto pendiente a ser revisado por un Admin o Maestro
        ]);

        // Podríamos enviar notificacion a un Email interno info@vocescriticas o Slack. 

        return response()->json([
            'message' => 'Gracias, el equipo lo revisará y aplicará las sanciones necesarias.'
        ], 201);
    }
}
