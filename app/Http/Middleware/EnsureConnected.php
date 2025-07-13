<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureConnected
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $targetUserId = $request->route('user')?->id ?? $request->get('user_id');

        if (!$targetUserId) {
            return response()->json([
                'success' => false,
                'message' => 'User not specified'
            ], 400);
        }

        if ($user->id === $targetUserId) {
            return $next($request);
        }

        if (!$user->isConnectedTo($targetUserId)) {
            return response()->json([
                'success' => false,
                'message' => 'You must be connected to this user to perform this action'
            ], 403);
        }

        return $next($request);
    }
}