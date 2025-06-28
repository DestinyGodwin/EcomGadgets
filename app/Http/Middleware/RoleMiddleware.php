<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
  public function handle(Request $request, Closure $next, string $roles): Response
{
    $roleArray = explode(',', $roles);

    if (!in_array(Auth::user()->role, $roleArray)) {
        return response()->json([
            'status' => 'fail',
            'message' => 'You are not allowed to perform this operation'
        ], 403);
    }

    return $next($request);
}
}
