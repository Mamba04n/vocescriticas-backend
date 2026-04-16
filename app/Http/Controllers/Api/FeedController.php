<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Services\FeedService;
use Illuminate\Http\Request;
use Throwable;

class FeedController extends Controller
{
    public function __construct(private FeedService $feedService)
    {
    }

    public function index(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 15), 50);

        try {
            $feed = $this->feedService->getFeedFor($request->user(), $perPage);

            return response()->json([
                'success' => true,
                'message' => 'Feed obtenido correctamente',
                'data' => $feed,
                'errors' => null,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener el feed',
                'data' => null,
                'errors' => ['server' => ['Error interno del servidor']],
            ], 500);
        }
    }
}
