<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\Submission;

class SubmissionController extends Controller
{
    // Estudiante sube su trabajo/tarea a una evaluaciÃ³n
        public function store(Request $request, Evaluation $evaluation)
    {
        if ($evaluation->due_date && \Carbon\Carbon::now()->gt($evaluation->due_date)) {
            return response()->json(['message' => 'El tiempo de entrega ha finalizado. No se permiten más envíos.'], 403);
        }

        $request->validate([
            'file' => 'required|file|max:15360', // MÃ¡x 15MB
        ]);

        $path = $request->file('file')->store('submissions', config('filesystems.default', 'public'));

        $submission = Submission::updateOrCreate(
            ['evaluation_id' => $evaluation->id, 'user_id' => $request->user()->id],
            ['file_path' => $path, 'grade' => null, 'feedback' => null]
        );

        return response()->json([
            'success' => true,
            'message' => 'Trabajo entregado correctamente.',
            'submission' => $submission
        ]);
    }

    // Profesor califica (grade) la entrega
    public function grade(Request $request, Submission $submission)
    {
        $maxGrade = $submission->evaluation->max_grade ?? 100;
        
        $validated = $request->validate([
            'grade' => 'required|numeric|min:0|max:' . $maxGrade,
            'feedback' => 'nullable|string'
        ]);

        $submission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'CalificaciÃ³n asignada correctamente.',
            'submission' => $submission->load('user')
        ]);
    }
}
