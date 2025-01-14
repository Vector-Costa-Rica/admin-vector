<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HandleErrorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            Log::error('Application Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method(),
                'request_headers' => $request->headers->all(),
                'request_params' => $request->all()
            ]);

            if (app()->environment('production')) {
                return redirect()
                    ->route('welcome')
                    ->with('error', 'Se produjo un error. Por favor, inténtelo de nuevo más tarde.');
            }

            throw $e;
        }
    }
}
