<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSecurityLevel
{
    /**
     * Reject the request if the authenticated user's security_group
     * is below the required level.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $level  Minimum security_group required
     */
    public function handle(Request $request, Closure $next, int $level): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasLevel($level)) {
            abort(403, "This area requires security level {$level} or higher.");
        }

        return $next($request);
    }
}
