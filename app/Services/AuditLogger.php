<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(string $activity, $subject = null, array $data = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'activity' => $activity,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'data' => $data,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
