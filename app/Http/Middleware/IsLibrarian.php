<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsLibrarian
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !in_array($request->user()->role, [1, 2])) {
            return response()->json([
                'message' => 'Unauthorized. Librarian or Admin access required.'
            ], 403);
        }

        return $next($request);
    }
}
