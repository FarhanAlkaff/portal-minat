<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminInactivityTimeout
{
    /**
     * Timeout duration in seconds (30 minutes = 1800 seconds).
     */
    public const TIMEOUT_SECONDS = 1800;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('admin_last_activity');
            $currentTime = now()->timestamp;

            if ($lastActivity && ($currentTime - $lastActivity) > self::TIMEOUT_SECONDS) {
                $user = Auth::user();
                if ($user) {
                    \App\Services\AuditLogger::log("Sesi admin {$user->email} berakhir otomatis karena tidak aktif selama 30 menit.");
                }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->is('xhr/*') || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'Sesi Anda telah berakhir karena tidak ada aktivitas selama 30 menit.',
                        'redirect' => route('admin.login'),
                    ], 401);
                }

                return redirect()->route('admin.login')
                    ->with('warning', 'Sesi Anda telah berakhir karena tidak ada aktivitas selama 30 menit demi keamanan. Silakan login kembali.');
            }

            // Update timestamp of last activity
            session(['admin_last_activity' => $currentTime]);
        }

        return $next($request);
    }
}
