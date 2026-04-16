<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceSpaCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigin = rtrim((string) env('FRONTEND_URL', 'https://frontend-nu-nine-65.vercel.app'), '/');
        $origin = rtrim((string) $request->headers->get('Origin', ''), '/');

        if ($request->isMethod('OPTIONS')) {
            $response = response('', 204);
        } else {
            /** @var Response $response */
            $response = $next($request);
        }

        if ($origin !== '' && $origin === $allowedOrigin) {
            $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Accept, Authorization, Content-Type, Origin, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN');
            $response->headers->set('Vary', 'Origin');
        }

        return $response;
    }
}
