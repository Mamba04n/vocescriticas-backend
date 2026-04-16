<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Evaluation;

class EvaluationController extends Controller
{
    public function index(Group $group)
    {
        // Enviar la base de evaluaciones y la cuenta o relaciÃ³n de entregas, junto al equipo de cada usuario
        $evaluations = $group->evaluations()->with([
            'submissions.user.teams' => function($q) use ($group) {
                $q->where('group_id', $group->id);
            }
        ])->get();
        return response()->json(['success' => true, 'evaluations' => $evaluations]);
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'max_grade' => 'nullable|numeric|min:0',
            'file' => 'nullable|file|max:20480',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'max_grade' => $validated['max_grade'] ?? 100,
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('evaluations', 'public');
        }

        $evaluation = $group->evaluations()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Evaluacion creada correctamente.',
            'evaluation' => $evaluation
        ], 201);
    }
}
