<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     * 
     * This middleware provides menu-level access control based on user roles
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Super admin has access to everything
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Get the current route name to determine module access
        $routeName = $request->route()->getName();
        
        // Define route patterns for each role
        $adminAllowedRoutes = [
            'dashboard',
            'system.users.*',
            'system.configurations.*',
            'system.menu.*',
            'system.audit-log.*',
            'system.news-events.*',
            'content.*',
            'settings.*'
        ];

        $editorAllowedRoutes = [
            'dashboard',
            'content.*',
            'system.news-events.*',
            'settings.profile.*',
            'settings.password.*'
        ];

        // Check admin access
        if ($user->hasRole('admin')) {
            foreach ($adminAllowedRoutes as $pattern) {
                if (fnmatch($pattern, $routeName)) {
                    return $next($request);
                }
            }
        }

        // Check editor access
        if ($user->hasRole('editor')) {
            foreach ($editorAllowedRoutes as $pattern) {
                if (fnmatch($pattern, $routeName)) {
                    return $next($request);
                }
            }
        }

        // If no role matches or route not allowed, return 403
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this module.',
            ], 403);
        }

        abort(403, 'You do not have access to this module.');
    }
}