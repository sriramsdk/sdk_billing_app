@extends('layouts.admin')

@section('title', 'Mail Logs')
@section('page-title', 'Mail Logs')

@section('content')
<div class="mb-8"><p class="text-sm text-slate-500">Invoice delivery activity</p><h2 class="mt-1 text-3xl font-black text-slate-900">Mail Logs</h2></div>
<form method="GET" class="mb-6 flex flex-col gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 md:flex-row">
    <input name="search" value="{{ request('search') }}" placeholder="Invoice, recipient, or subject" class="flex-1 rounded-xl border border-slate-300 px-4 py-3">
    <select name="status" class="rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">All statuses</option><option value="queued" @selected(request('status') === 'queued')>Queued</option><option value="sent" @selected(request('status') === 'sent')>Sent</option><option value="failed" @selected(request('status') === 'failed')>Failed</option></select>
    <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white">Search</button>
    <a href="{{ route('admin.mail-logs') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-600">Reset</a>
</form>
<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200"><div class="overflow-x-auto"><table class="w-full min-w-[950px] text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-6 py-4">Invoice</th><th class="px-6 py-4">Recipient</th><th class="px-6 py-4">Subject</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">Sent / Created</th><th class="px-6 py-4">Error</th></tr></thead><tbody class="divide-y">@forelse($mailLogs as $mail)<tr class="hover:bg-slate-50"><td class="px-6 py-4 font-bold">{{ $mail->order?->invoice_number ?? '—' }}</td><td class="px-6 py-4">{{ $mail->recipient }}</td><td class="px-6 py-4">{{ $mail->subject }}</td><td class="px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $mail->status === 'sent' ? 'bg-emerald-50 text-emerald-700' : ($mail->status === 'failed' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($mail->status) }}</span></td><td class="px-6 py-4 text-slate-500">{{ ($mail->sent_at ?? $mail->created_at)->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</td><td class="max-w-xs truncate px-6 py-4 text-xs text-red-600">{{ $mail->error_message ?: '—' }}</td></tr>@empty<tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">No mail logs found.</td></tr>@endforelse</tbody></table></div><div class="border-t px-6 py-4">{{ $mailLogs->links() }}</div></div>
@endsection