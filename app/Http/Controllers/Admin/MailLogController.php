<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MailLogController extends Controller
{
    public function index(Request $request): View
    {
        $mailLogs = MailLog::with(['order', 'user'])
            ->when($request->search, fn ($query, $search) => $query->where(function ($query) use ($search): void {
                $query->where('recipient', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('order', fn ($order) => $order->where('invoice_number', 'like', "%{$search}%"));
            }))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.mail-logs.index', compact('mailLogs'));
    }
}