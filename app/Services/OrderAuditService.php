<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class OrderAuditService
{
    public function record(string $action, object $order, array $newValues = [], ?array $oldValues = null): AuditLog
    {
        $request = app()->bound('request') ? request() : null;

        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => 'orders',
            'auditable_type' => $order::class,
            'auditable_id' => $order->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}