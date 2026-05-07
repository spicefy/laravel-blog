@extends('layouts.admin')

@section('content')
<div class="bg-gray-900 px-6 lg:px-8 py-6 space-y-5">

    {{-- ── PAGE HEADER ──────────────────────────────────────────── --}}
     <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-white tracking-tight">System Monitoring</h1>
            <p class="text-sm text-gray-500 mt-0.5">Real-time log analysis &amp; audit trail</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search all logs…"
                    class="pl-9 pr-4 py-2 text-sm bg-gray-900 border border-gray-800 rounded-lg text-gray-200 placeholder:text-gray-600 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/40 w-60 transition">
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg transition">Search</button>
            @if($search)
                <a href="{{ route('admin.logs.index') }}" class="px-3 py-2 text-sm text-gray-400 hover:text-white border border-gray-800 rounded-lg transition">Clear</a>
            @endif
        </form>
    </div>

    {{-- ── STAT CARDS ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest text-gray-500">Total Entries</span>
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-800 text-gray-400">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-white tabular-nums">{{ number_format($totalEntries) }}</p>
            <p class="text-xs text-gray-600 mt-1">across all channels</p>
        </div>

        <div class="bg-gray-900 border border-red-900/50 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest text-red-500/80">Errors</span>
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-950 text-red-500">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-red-400 tabular-nums">{{ number_format($totalErrors) }}</p>
            <p class="text-xs text-gray-600 mt-1">critical &amp; above</p>
        </div>

        <div class="bg-gray-900 border border-yellow-900/50 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest text-yellow-500/80">Warnings</span>
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-950 text-yellow-500">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-yellow-400 tabular-nums">{{ number_format($totalWarnings) }}</p>
            <p class="text-xs text-gray-600 mt-1">need attention</p>
        </div>

        <div class="bg-gray-900 border border-blue-900/50 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest text-blue-500/80">Info</span>
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-950 text-blue-400">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-blue-400 tabular-nums">{{ number_format($totalInfo) }}</p>
            <p class="text-xs text-gray-600 mt-1">informational</p>
        </div>

    </div>

    {{-- ── CHART ─────────────────────────────────────────────────── --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-sm font-semibold text-white">Errors Per Day</h2>
                <p class="text-xs text-gray-500 mt-0.5">Last 14 days — ERROR, CRITICAL, ALERT, EMERGENCY</p>
            </div>
            <span class="text-xs font-medium text-red-400 bg-red-950 border border-red-900/60 px-2.5 py-1 rounded-full">
                {{ array_sum($errorsPerDay['values']) }} total
            </span>
        </div>
        <div class="relative h-52">
            <canvas id="errorsChart"></canvas>
        </div>
    </div>

    {{-- ── TABBED LOG TABLES ─────────────────────────────────────── --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden" x-data="{ tab: 'system' }">

        {{-- Tab bar --}}
        <div class="flex border-b border-gray-800 overflow-x-auto">

            <button @click="tab = 'system'"
                :class="tab === 'system' ? 'border-b-2 border-indigo-500 text-white bg-gray-800/50' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/30'"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap transition-colors">
                System Logs
                <span :class="tab === 'system' ? 'bg-indigo-500/20 text-indigo-300' : 'bg-gray-800 text-gray-500'"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full tabular-nums transition-colors">{{ count($systemLogs) }}</span>
            </button>

            <button @click="tab = 'activity'"
                :class="tab === 'activity' ? 'border-b-2 border-emerald-500 text-white bg-gray-800/50' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/30'"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap transition-colors">
                User Activity
                <span :class="tab === 'activity' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-gray-800 text-gray-500'"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full tabular-nums transition-colors">{{ count($activityLogs) }}</span>
            </button>

            <button @click="tab = 'audit'"
                :class="tab === 'audit' ? 'border-b-2 border-amber-500 text-white bg-gray-800/50' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/30'"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap transition-colors">
                Audit Trail
                <span :class="tab === 'audit' ? 'bg-amber-500/20 text-amber-300' : 'bg-gray-800 text-gray-500'"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full tabular-nums transition-colors">{{ count($auditLogs) }}</span>
            </button>

            <button @click="tab = 'api'"
                :class="tab === 'api' ? 'border-b-2 border-sky-500 text-white bg-gray-800/50' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/30'"
                class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap transition-colors">
                API Requests
                <span :class="tab === 'api' ? 'bg-sky-500/20 text-sky-300' : 'bg-gray-800 text-gray-500'"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full tabular-nums transition-colors">{{ count($apiLogs) }}</span>
            </button>

        </div>

        {{-- ── SYSTEM LOGS ──────────────────────────────────────── --}}
        <div x-show="tab === 'system'" x-cloak>
            <div class="overflow-x-auto overflow-y-auto max-h-[500px]">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10 bg-gray-900">
                        <tr class="border-b border-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-40">Timestamp</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-24">Level</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">Message</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-16">Env</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @forelse($systemLogs as $log)
                            @php
                                $lc = match($log['level']) {
                                    'ERROR','CRITICAL','ALERT','EMERGENCY' => 'text-red-400 bg-red-950 border-red-900/50',
                                    'WARNING' => 'text-yellow-400 bg-yellow-950 border-yellow-900/50',
                                    'INFO'    => 'text-blue-400 bg-blue-950 border-blue-900/50',
                                    default   => 'text-gray-400 bg-gray-800 border-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-800/40 transition-colors group">
                                <td class="px-4 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">{{ $log['timestamp'] }}</td>
                                <td class="px-4 py-3"><span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded border {{ $lc }}">{{ $log['level'] }}</span></td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-300 max-w-xl"><span class="block truncate group-hover:whitespace-normal group-hover:break-words">{{ $log['message'] }}</span></td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $log['env'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-14 text-center text-sm text-gray-600">No system logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── USER ACTIVITY ────────────────────────────────────── --}}
        <div x-show="tab === 'activity'" x-cloak>
            <div class="overflow-x-auto overflow-y-auto max-h-[500px]">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10 bg-gray-900">
                        <tr class="border-b border-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-40">Timestamp</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-24">Level</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-36">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">Event</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-28">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @forelse($activityLogs as $log)
                            @php
                                $lc = match($log['level']) {
                                    'ERROR','CRITICAL','ALERT','EMERGENCY' => 'text-red-400 bg-red-950 border-red-900/50',
                                    'WARNING' => 'text-yellow-400 bg-yellow-950 border-yellow-900/50',
                                    'INFO'    => 'text-blue-400 bg-blue-950 border-blue-900/50',
                                    default   => 'text-gray-400 bg-gray-800 border-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-800/40 transition-colors group">
                                <td class="px-4 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">{{ $log['timestamp'] }}</td>
                                <td class="px-4 py-3"><span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded border {{ $lc }}">{{ $log['level'] }}</span></td>
                                <td class="px-4 py-3">
                                    @if($log['user'])
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-emerald-900 text-emerald-400 text-xs font-bold flex items-center justify-center flex-shrink-0">{{ strtoupper(substr($log['user'], 0, 1)) }}</span>
                                            <span class="text-xs text-gray-300 truncate">{{ $log['user'] }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-300 max-w-sm"><span class="block truncate group-hover:whitespace-normal group-hover:break-words">{{ $log['message'] }}</span></td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $log['ip'] ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-14 text-center text-sm text-gray-600">No user activity logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── AUDIT TRAIL ──────────────────────────────────────── --}}
        <div x-show="tab === 'audit'" x-cloak>
            <div class="overflow-x-auto overflow-y-auto max-h-[500px]">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10 bg-gray-900">
                        <tr class="border-b border-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-40">Timestamp</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-36">Who</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-28">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @forelse($auditLogs as $log)
                            @php
                                $action = strtolower($log['action'] ?? '');
                                $ac = match(true) {
                                    str_contains($action, 'delet') || str_contains($action, 'remov') => 'text-red-400 bg-red-950 border-red-900/50',
                                    str_contains($action, 'creat') || str_contains($action, 'store') => 'text-emerald-400 bg-emerald-950 border-emerald-900/50',
                                    str_contains($action, 'updat') || str_contains($action, 'edit')  => 'text-amber-400 bg-amber-950 border-amber-900/50',
                                    default => 'text-gray-400 bg-gray-800 border-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-800/40 transition-colors group">
                                <td class="px-4 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">{{ $log['timestamp'] }}</td>
                                <td class="px-4 py-3">
                                    @if($log['user'])
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-amber-900 text-amber-400 text-xs font-bold flex items-center justify-center flex-shrink-0">{{ strtoupper(substr($log['user'], 0, 1)) }}</span>
                                            <span class="text-xs text-gray-300 truncate">{{ $log['user'] }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-600">System</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3"><span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded border {{ $ac }}">{{ ucfirst($log['action'] ?? 'unknown') }}</span></td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-300 max-w-sm"><span class="block truncate group-hover:whitespace-normal group-hover:break-words">{{ $log['message'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-14 text-center text-sm text-gray-600">No audit entries found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── API REQUESTS ─────────────────────────────────────── --}}
        <div x-show="tab === 'api'" x-cloak>
            <div class="overflow-x-auto overflow-y-auto max-h-[500px]">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10 bg-gray-900">
                        <tr class="border-b border-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-40">Timestamp</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-16">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">URL / Message</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-20">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-24">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500 w-28">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @forelse($apiLogs as $log)
                            @php
                                $status = $log['status'] ?? null;
                                $sc = match(true) {
                                    $status >= 500 => 'text-red-400 bg-red-950 border-red-900/50',
                                    $status >= 400 => 'text-orange-400 bg-orange-950 border-orange-900/50',
                                    $status >= 300 => 'text-yellow-400 bg-yellow-950 border-yellow-900/50',
                                    $status >= 200 => 'text-emerald-400 bg-emerald-950 border-emerald-900/50',
                                    default        => 'text-gray-400 bg-gray-800 border-gray-700',
                                };
                                $method = strtoupper($log['method'] ?? '');
                                $mc = match($method) {
                                    'GET'         => 'text-sky-400',
                                    'POST'        => 'text-emerald-400',
                                    'PUT','PATCH' => 'text-amber-400',
                                    'DELETE'      => 'text-red-400',
                                    default       => 'text-gray-400',
                                };
                                $ms = (int)($log['duration'] ?? 0);
                                $dc = $ms > 2000 ? 'text-red-400' : ($ms > 500 ? 'text-yellow-400' : 'text-gray-400');
                            @endphp
                            <tr class="hover:bg-gray-800/40 transition-colors group">
                                <td class="px-4 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">{{ $log['timestamp'] }}</td>
                                <td class="px-4 py-3 font-mono text-xs font-bold {{ $mc }}">{{ $method ?: '—' }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-300 max-w-xs">
                                    @if($log['url'])
                                        <span class="block truncate group-hover:whitespace-normal group-hover:break-words text-sky-300">{{ $log['url'] }}</span>
                                    @else
                                        <span class="block truncate group-hover:whitespace-normal group-hover:break-words">{{ $log['message'] }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($status)
                                        <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded border {{ $sc }}">{{ $status }}</span>
                                    @else
                                        <span class="text-xs text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs tabular-nums">
                                    @if($log['duration'])
                                        <span class="{{ $dc }}">{{ $ms }}ms</span>
                                    @else
                                        <span class="text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $log['ip'] ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-14 text-center text-sm text-gray-600">No API request logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ── FOOTER ───────────────────────────────────────────────── --}}
    <p class="text-center text-xs text-gray-700 pb-2">
        Monitoring &mdash; {{ now()->format('D, d M Y H:i:s') }} &mdash; {{ number_format($totalEntries) }} entries loaded
    </p>

</div>
</div>
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const labels = @json($errorsPerDay['labels']);
    const values = @json($errorsPerDay['values']);
    const formatted = labels.map(d => {
        const [y, m, day] = d.split('-');
        return new Date(y, m - 1, day).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    const ctx = document.getElementById('errorsChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(239,68,68,0.35)');
    gradient.addColorStop(1, 'rgba(239,68,68,0.01)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: formatted,
            datasets: [{
                label: 'Errors',
                data: values,
                borderColor: 'rgb(239,68,68)',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: 'rgb(239,68,68)',
                pointBorderColor: 'rgb(3,7,18)',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgb(17,24,39)',
                    borderColor: 'rgb(55,65,81)',
                    borderWidth: 1,
                    titleColor: 'rgb(209,213,219)',
                    bodyColor: 'rgb(239,68,68)',
                    padding: 10,
                    callbacks: { label: ctx => ` ${ctx.parsed.y} error${ctx.parsed.y !== 1 ? 's' : ''}` }
                },
            },
            scales: {
                x: {
                    grid: { color: 'rgba(55,65,81,0.5)' },
                    ticks: { color: 'rgb(107,114,128)', font: { size: 11 } },
                    border: { display: false },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(55,65,81,0.5)' },
                    ticks: { color: 'rgb(107,114,128)', font: { size: 11 }, stepSize: 1, precision: 0 },
                    border: { display: false },
                },
            },
        }
    });
})();
</script>
@endpush
@endsection

{{-- ── DASHBOARD WIDGETS (from dashboard.blade.php) ── --}}